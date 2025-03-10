<div class="flex flex-col h-screen" x-data="{
    text: '',
    temporaryMessage: '',
    sending: false
}">
    <!-- Messages Area - Scrollable -->
    <div class="flex-1 overflow-y-auto">
        <div class="z-50 fixed top-4 right-4">
            <!-- Setting -->
            <livewire:components.settings-modal :user="$user" />
        </div>
        <div class="max-w-3xl mx-auto p-4 h-full">
            @if ($conversation->messages->isEmpty())
                <div class="text-center text-gray-300 h-full flex justify-center items-center ">
                    <div class="flex justify-center items-center">
                        <h1 x-data="typingEffectInHome()" x-init="startTyping()" x-text="displayText"
                            class="text-3xl sm:text-4xl font-semibold text-center mb-6"></h1>
                    </div>
                </div>
            @else
                <ul class="space-y-5">
                    @foreach ($messages as $message)
                        <li class="{{ $message->is_user_message ? 'flex justify-end' : 'flex justify-start' }}">
                            <!-- Bagian Prompt User -->
                            @if ($message->is_user_message)
                                <div
                                    class="message bg-cyan-950  border-zinc-300 px-4 py-2 text-white max-w-xl rounded-lg">
                                    {!! $message->content !!}
                                </div>
                            @else
                                <!--Bagian Respon AI -->
                                <div>
                                    <div id="aiResponse-{{ $loop->index }}"
                                        class="message text-white max-w-3xl p-3 rounded-lg chat-container">
                                        <!-- Respon AI -->
                                        {!! $message->content !!}
                                    </div>
                                    <!--rating -->
                                    <div
                                        class="mr-2 mt-1 flex flex-col-reverse justify-between gap-2 text-slate-500 sm:flex-row">
                                        {{-- button salin --}}
                                        <div class="inline-block">
                                            <button id="copyButton-{{ $loop->index }}"
                                                onclick="copyText('aiResponse-{{ $loop->index }}', 'copyButton-{{ $loop->index }}')"
                                                class="hover:text-blue-600 focus:outline-none">

                                                <!-- Ikon Salin -->
                                                <svg id="copyIcon-{{ $loop->index }}"
                                                    xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z">
                                                    </path>
                                                    <path
                                                        d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2">
                                                    </path>
                                                </svg>

                                                <!-- Ikon Ceklis -->
                                                <svg id="checkIcon-{{ $loop->index }}"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5 hidden text-green-500" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M5 12l5 5l10 -10"></path>
                                                </svg>
                                            </button>
                                            <!-- Tooltip -->
                                            <span id="tooltip-{{ $loop->index }}"
                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1 p-1 text-xs text-white bg-black rounded opacity-0 transition-opacity duration-300">
                                                Teks disalin!
                                            </span>
                                        </div>
                                        {{-- <livewire:components.message-rating :message="$message" :key="$message->id" /> --}}
                                    </div>
                                </div>
                            @endif
                        </li>
                    @endforeach
                    <!-- Temporary Message - Menggunakan state temporaryMessage -->
                    <template x-if="temporaryMessage">
                        <li class="flex justify-end">
                            <div class="bg-gray-300 text-black max-w-lg p-3 rounded-lg">
                                <p x-text="temporaryMessage"></p>
                            </div>
                        </li>
                    </template>
                    <!-- Loading/Sending Message - Menggunakan state sending -->
                    <template x-if="sending">
                        <li class="flex justify-start">
                            <div class=" text-white max-w-lg p-3 rounded-lg chat-container"
                                wire:stream="response">
                                {!! $answer !!}
                            </div>
                        </li>
                    </template>
                </ul>
            @endif

        </div>
    </div>

    <!-- Input Area - Fixed at Bottom -->
    <div class="flex items-center justify-center mt-2">
        <div class="flex-none mb-4 px-4 py-2 w-3/5 bg-gray-800 rounded-3xl">
            <form
                x-on:submit.prevent="
                temporaryMessage = text;
                sending = true;
                $wire.sendMessage(text).then(() => {
                    $wire.respond().then(() => {
                        sending = false;
                        temporaryMessage = null;
    
                        $wire.clearState();
                    })
                });
                text = '';
            ">
                <!-- Text Area for Input and Button -->
                <div class="flex w-full items-center space-x-2">
                    <livewire:components.chat-panel/>
                    <textarea name="message" id="message" x-model="text"
                        class="flex-grow w-full p-3 bg-zinc-800 text-gray-300 border border-zinc-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition"
                        placeholder="Type your message here..." rows="1"></textarea>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition transform hover:scale-105">
                        Send
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function typingEffectInHome() {
            return {
                text: "What can I help with?", // Teks yang akan diketik
                displayText: "", // Teks yang akan muncul di layar
                index: 0, // Indeks karakter yang akan diketik selanjutnya
                typingSpeed: 100, // Kecepatan mengetik (ms)

                // Fungsi untuk memulai efek typing
                startTyping() {
                    this.typeCharacter();
                },

                // Fungsi untuk mengetik karakter per karakter
                typeCharacter() {
                    if (this.index < this.text.length) {
                        this.displayText += this.text.charAt(this.index);
                        this.index++;
                        setTimeout(() => this.typeCharacter(), this.typingSpeed);
                    }
                }
            }
        }
    </script>
</div>
