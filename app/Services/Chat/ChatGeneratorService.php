<?php

namespace App\Services\Chat;

use App\Models\Conversation;
use OpenAI\Laravel\Facades\OpenAI;
use App\Services\Search\VectorSearchService;

class ChatGeneratorService
{
    private VectorSearchService $vectorSearchService;

    public function __construct(VectorSearchService $vectorSearchService)
    {
        $this->vectorSearchService = $vectorSearchService;
    }

    public function generateWithRAGPersona(Conversation $conversation, array $personaContext)
    {
        $lastMessage = $conversation->messages->last();
        $query = $lastMessage->content;

        $relevantDocument = $this->vectorSearchService->findRelevantDocumentsWithContext($query, 5);

        $relevantContent = $relevantDocument->map(function ($doc) {
            return [
                'id' => $doc['id'],
                'document_id' => $doc['document_id'],
                'title' => $doc['title'],
                'content' => $doc['content'],
                'metadata' => $doc['metadata'],
                'similarity_score' => $doc['similarity_score'],
                'chunk_order' => $doc['chunk_order'],
            ];
        });

        $relevantDocumentArray = [
            'role' => 'system',
            'content' => $relevantContent->pluck('content')->implode("\n\n")
        ];

        $conversationMessages = $conversation->messages->map(function ($message) {
            return [
                'role' => $message->is_user_message ? 'user' : 'assistant',
                'content' => $message->content
            ];
        })->toArray();

        //prompt pembantu
        $metadataString = $relevantContent->pluck('metadata')->implode(', ');
        $prompt = [
            'role' => 'system',
            'content' => 'Berdasarkan percakapan sebelumnya, berikut adalah dokumen relevan yang mungkin bisa membantu Anda. Gunakan dokumen ini hanya sebagai informasi tambahan untuk mendukung pemahaman, bukan sebagai sumber utama jawaban. Fokuskan jawaban Anda pada pertanyaan pengguna berikut: ' . $metadataString
        ];

        $allMessages = array_merge($personaContext, $conversationMessages, [$prompt],  [$relevantDocumentArray]);

        return $this->createStreamResponse($allMessages);
    }

    public function generateWithRAG(Conversation $conversation)
    {
        $lastMessage = $conversation->messages->last();
        $query = $lastMessage->content;

        $relevantDocument = $this->vectorSearchService->findRelevantDocumentsWithContext($query, 3);

        $relevantContent = $relevantDocument->map(function ($doc) {
            return [
                'id' => $doc['id'],
                'document_id' => $doc['document_id'],
                'title' => $doc['title'],
                'content' => $doc['content'],
                'metadata' => $doc['metadata'],
                'similarity_score' => $doc['similarity_score'],
                'chunk_order' => $doc['chunk_order'],
            ];
        });

        $relevantDocumentArray = [
            'role' => 'system',
            'content' => $relevantContent->pluck('content')->implode("\n\n")
        ];


        $conversationMessages = $conversation->messages->map(function ($message) {
            return [
                'role' => $message->is_user_message ? 'user' : 'assistant',
                'content' => $message->content
            ];
        })->toArray();

        $metadataString = $relevantContent->pluck('metadata')->implode(', ');

        $prompt = [
            'role' => 'system',
            'content' => 'Berdasarkan percakapan sebelumnya, berikut adalah dokumen relevan yang mungkin bisa membantu Anda. Gunakan dokumen ini hanya sebagai informasi tambahan untuk mendukung pemahaman, bukan sebagai sumber utama jawaban. Fokuskan jawaban Anda pada pertanyaan pengguna berikut : ' . $metadataString
        ];

        $allMessages = array_merge($conversationMessages, [$prompt], [$relevantDocumentArray]);


        return $this->createStreamResponse($allMessages);
    }

    public function generateResponseAI(Conversation $conversation)
    {
        $conversationMessages = $conversation->messages->map(function ($message) {
            return [
                'role' => $message->is_user_message ? 'user' : 'assistant',
                'content' => $message->content
            ];
        })->toArray();

        return $this->createStreamResponse($conversationMessages);
    }

    private function createStreamResponse(array $messages)
    {
        return OpenAI::chat()->createStreamed([
            'model' => 'gpt-3.5-turbo-0125',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ]);
    }

    public function generateTitle(array $messages): string
    {
        // Filter out system messages (persona context) for title generation
        $userMessages = array_filter($messages, function ($message) {
            return $message['role'] !== 'system';
        });

        $prompt = [
            'role' => 'system',
            'content' => 'Create a title based on previous messages, without anything but the title. Title should be without quotation marks and not be prefixed with anything like "Title:"'
        ];

        $messagesWithInstruction = array_merge([$prompt], array_values($userMessages));

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo-0125',
            'messages' => $messagesWithInstruction,
            'temperature' => 0.7,
            'max_tokens' => 60,
        ]);

        if (!empty($response->choices)) {
            return trim($response->choices[0]->message->content) ?: 'New Chat';
        }


        return 'New Chat';
    }
}
