<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Personas;

class SettingsModal extends Component
{

    //user
    public $user;
    //model
    public $selectedModel;

    //current Conversation
    public $currentConversation;

    //selected persona
    public $selectedPersona;

    //must select persona
    public $mustSelectPersona;

    //available models
    public $availableModels = [
        'gpt-3.5-turbo-0025' => 'GPT-3.5 Turbo',
        'gpt-3.5-turbo-0125 + rag' => 'GPT-3.5 Turbo + RAG',
        'gpt-3.5-turbo-0125 + rag + persona' => 'GPT-3.5 Turbo + RAG + Persona',
    ];

    // listeners
    protected $listeners = ['conversationChanged' => 'loadCurrentConversation'];

    //mount
    public function mount()
    {
        $this->user = auth()->user();
        $this->loadCurrentConversation();

        //load selected persona
        $this->selectedPersona = $this->user->persona_id;
        
        $this->mustSelectPersona = is_null($this->user->persona_id);
    }

    public function loadCurrentConversation()
    {
        $uuid = session('current_conversation_uuid');
        $this->currentConversation = $this->user->conversations()
            ->where('uuid', $uuid)
            ->first();

        if ($this->currentConversation) {
            $this->selectedModel = $this->currentConversation->model;
        }
    }


    public function saveModelSetting()
    {
        if (!$this->currentConversation) {
            session()->flash('error', 'Tidak ada percakapan yang aktif');
            return;
        }

        if ($this->currentConversation->messages()->count() > 0) {
            session()->flash('error', 'Model tidak dapat diubah setelah percakapan dimulai');
            return;
        }

        $this->currentConversation->update([
            'model' => $this->selectedModel
        ]);

        $this->dispatch('modelUpdated', $this->selectedModel);
        session()->flash('success', 'Model AI berhasil diperbarui');
    }



    //delete all conversations
    public function deleteAllConversations()
    {
        $this->user->conversations->each->delete();
        //pangil function createConversation untuk membuat conversation baru dan pergi ke route conversation baru itu
        $this->createConversation();
    }

    //jika function deleteAllConversations dijalankan maka buat conversation baru dan pergi ke route conversation baru itu
    public function createConversation()
    {
        $conversation = $this->user->conversations()->create([
            'model' => 'gpt-3.5-turbo-0125' // Tentukan default model di sini
        ]);
        return redirect()->route('chat.show', $conversation);
    }

    //savePersonaSetting
    public function savePersonaSetting()
    {
        // Validasi persona yang dipilih
        $this->validate([
            'selectedPersona' => 'required|exists:personas,id'
        ]);
    
        // Update user dengan persona yang dipilih
        $this->user->update([
            'persona_id' => $this->selectedPersona
        ]);

        //reset flag
        $this->mustSelectPersona = false;
    
        // Kirim notifikasi berhasil
        session()->flash('success', 'Persona berhasil diperbarui');
    
        // Dispatch event jika diperlukan
        $this->dispatch('personaUpdated', $this->selectedPersona);
    }


    public function render()
    {
        return view(
            'livewire.components.settings-modal',
            [
                'personas' => Personas::all(),
            ]
        );
    }


}
