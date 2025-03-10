<?php

namespace App\Livewire\Components;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Personas;
use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use App\Services\Search\VectorSearchService;
use App\Services\Chat\ChatGeneratorService;

class ChatBot extends Component
{
    public Conversation $conversation;
    public $messages = [];
    public string $model;
    public $user;
    public $answer = null;
    public bool $responding = false;
    protected $listeners = [
        'messageAdded' => 'loadMessages',
        'conversationListUpdated' => 'refreshConversation',
        'modelUpdated' => 'updateModel'
    ];

    private VectorSearchService $vectorSearchService;
    private ChatGeneratorService $chatGeneratorService;

    public function boot(
        VectorSearchService $vectorSearchService,
        ChatGeneratorService $chatGeneratorService
    ) {
        $this->vectorSearchService = $vectorSearchService;
        $this->chatGeneratorService = $chatGeneratorService;
    }

    public function mount(Conversation $conversation)
    {
        session(['current_conversation_uuid' => $conversation->uuid]);
        $this->conversation = $conversation;
        $this->loadMessages();
        $this->resetChat();
        $this->user = auth()->user();
        $this->model = $conversation->model;
    }

    public function searchRelevantDocuments(string $query)
    {
        // Pastikan vectorSearchService telah terinisialisasi
        if (!$this->vectorSearchService) {
            $this->vectorSearchService = app(VectorSearchService::class);
        }

        // Cari dokumen yang relevan dengan query
        return $this->vectorSearchService->findRelevantDocumentsWithContext($query, 3);
    }


    private function getPersonaContext(): ?array
    {
        $user = auth()->user();
        if ($user && $user->persona_id) {
            $persona = Personas::find($user->persona_id);
            if ($persona) {
                return [
                    [
                        'role' => 'system',
                        'content' => $persona->ai_prompt . "\n" . $persona->description
                    ]
                ];
            }
        }
        return [];
    }

    //fungsi untuk mengetahui model yang digunakan
    public function getModel(): string
    {
        return $this->model ?? 'gpt-3.5-turbo-0125';
    }

    //fungsi untuk mengupdate model
    public function updateModel($model)
    {
        $this->model = $model;
    }

    public function refreshConversation()
    {
        // Pastikan bahwa percakapan yang dipilih masih ada
        if (!auth()->user()->conversations()->find($this->conversation->id)) {
            $this->conversation = auth()->user()->conversations()->create([]);
            return redirect()->route('chat.show', $this->conversation);
        }
        $this->loadMessages();
        $this->resetChat();
    }

    public function loadMessages(): void
    {
        $this->messages = $this->conversation->messages()->oldest()->get()->map(function (Message $message) {
            $message->content = app(MarkdownRenderer::class)
                ->highlightTheme('github-dark')
                ->toHtml($message->content);

            return $message;
        });
    }

    public function sendMessage($text): void  // untuk menyimpan pesan user ke database
    {
        $this->conversation->messages()->create([
            'content' => $text,
            'is_user_message' => true
        ]);
    }


    //respond method
    public function respond(): void
    {
        $model = $this->getModel();

        // Generate title if needed sebelum generate response
        if (is_null($this->conversation->title)) {
            $conversationMessages = $this->conversation->messages->map(function ($message) {
                return [
                    'role' => $message->is_user_message ? 'user' : 'assistant',
                    'content' => $message->content
                ];
            })->toArray();

            $title = $this->chatGeneratorService->generateTitle($conversationMessages);
            $this->js("document.title = '{$title}'");
            $this->conversation->update(['title' => $title]);
        }

        $stream = match ($model) {
            'gpt-3.5-turbo-0125 + rag + persona' => $this->chatGeneratorService->generateWithRAGPersona(
                $this->conversation,
                $this->getPersonaContext()
            ),
            'gpt-3.5-turbo-0125 + rag' => $this->chatGeneratorService->generateWithRAG($this->conversation),
            default => $this->chatGeneratorService->generateResponseAI($this->conversation)
        };

        $entireMessage = '';

        foreach ($stream as $response) {
            $this->responding = false;
            $this->answer = $response->choices[0]->delta->content;
            $entireMessage .= $this->answer;
            $this->stream(to: 'response', content: $this->answer);
        }

        $this->answer = $entireMessage;

        $this->conversation->messages()->create([
            'content' => $entireMessage,
            'is_user_message' => false
        ]);

        $this->dispatch('messageAdded');
    }


    public function clearState(): void
    {
        $this->responding = false;
        $this->answer = null;
    }

    //resetChat method
    public function resetChat() // untuk mereset chat dan  menghilangkan konten chat
    {
        $this->answer = null;
        $this->responding = false;
        $this->messages = [];
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.components.chat-bot');
    }
}
