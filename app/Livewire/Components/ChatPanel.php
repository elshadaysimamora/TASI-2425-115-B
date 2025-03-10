<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ChatPanel extends Component
{

    public $conversations;
    public $user;
    public $editingConversationId = null;
    public $conversationToDelete = null;
    protected $listeners = [
        'conversationListUpdated' => 'loadConversations',
    ];



    public function handleConversationDeleted($data = null)
    {
        $this->loadConversations(); // Refresh conversations
    }


    public function mount()
    {
        $this->loadConversations();
        $this->user = auth()->user();
    }

    public function loadConversations()
    {
        $this->conversations = auth()->user()->conversations()
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function createNewChat()
    {
        $conversation = auth()->user()->conversations()->create([
            'model' => 'gpt-3.5-turbo-0125',
        ]);
        $this->loadConversations();
        return redirect()->route('chat.show', $conversation);
    }

    public function render()
    {
        return view('livewire.components.chat-panel');
    }
}
