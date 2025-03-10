<div x-data="{ isModalSetting: false, confirmationDelete: false, confirmationLogout: false, selectStudentPersona: false, toastMessage: false }">
    <div class="mt-auto w-full space-y-4 px-2 py-4">
        <div
            class="flex w-full gap-x-2 rounded-lg text-left text-sm font-medium text-white transition-colors duration-200 hover:bg-slate-200 focus:outline-none dark:text-slate-200 dark:hover:bg-slate-800">
            <button @click="isModalSetting = true" type="button" class="">
                <img class="size-10 rounded-full" src="{{ $user->profile_photo_url ?? 'default-profile-photo-url' }}"
                    alt="{{ $user->name }}">
                <span class="sr-only">Your profile</span>
            </button>
        </div>

        <!-- Modal Setting -->
        <div x-show="isModalSetting" class="relative z-10 w-full" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 w-1/2 sm:p-6">
                        <main class="flex-1">
                            <div class="absolute right-0 top-0 hidden p-4 sm:block">
                                <button type="button" @click="isModalSetting = false"
                                    class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="sr-only">Close</span>
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" aria-hidden="true" data-slot="icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Settings</h1>
                            </div>
                            <div class="flex items-center justify-between mt-8 space-x-4 p-4 bg-gray-100 rounded-lg">
                                <a href="/user/profile"
                                    class="flex items-center space-x-4 hover:bg-slate-200 p-2 rounded-md">
                                    <!-- Avatar -->
                                    <img src="{{ $user->profile_photo_url ?? 'default-profile-photo-url' }}"
                                        alt="Avatar" class="w-12 h-12 rounded-full">

                                    <!-- Account Info -->
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">
                                            {{ $user->name ?? 'default-profile-photo-url' }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </a>

                                <!-- Log out Button -->
                                <button type="submit" @click="confirmationLogout = true ; isModalSetting = false"
                                    class="px-4 py-2 text-sm font-medium text-indigo-600 bg-purple-100 rounded-full hover:bg-purple-200">
                                    Log out
                                </button>
                            </div>
                            <div class="px-4 sm:px-6 lg:px-0 mt-8">
                                <div>
                                    <!-- Tab Content -->
                                    <div class="mt-4 divide-y divide-gray-200">
                                        <div class="divide-y divide-gray-200">
                                            <div class="px-4 sm:px-6">
                                                <ul role="list" class="divide-gray-200">
                                                    <li class="flex items-center justify-between py-4">
                                                        <div class="flex flex-col">
                                                            <p class="text-sm/6 font-medium text-gray-900">
                                                                Model AI
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <select wire:model="selectedModel"
                                                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                                @foreach ($availableModels as $value => $label)
                                                                    <option value="{{ $value }}">
                                                                        {{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <li class="flex items-center justify-between py-4 border-t">
                                                        <p class="text-sm font-medium text-gray-900">Delete all chats
                                                        </p>
                                                        <button type="button"
                                                            @click="confirmationDelete = true; isModalSetting = false"
                                                            class="rounded-full bg-red-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                                                            Delete all
                                                        </button>
                                                    </li>
                                                    <li class="flex items-center justify-between py-4 border-t">
                                                        <p class="text-sm font-medium text-gray-900">Pilih Persona Yang
                                                            Merepresentasikan Anda
                                                        </p>
                                                        <button type="button"
                                                            @click="selectStudentPersona = true; isModalSetting = false"
                                                            class="rounded-full bg-red-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                                                            Explore
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="mt-4">
                                                <div class="flex justify-end px-4 py-4 space-x-4">
                                                    <button @click="isModalSetting = false"
                                                        class="px-4 py-2 text-sm font-medium text-gray-900 bg-gray-100 rounded-md hover:bg-gray-200">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"wire:click="saveModelSetting"
                                                        @click="isModalSetting = false; toastMessage = true"
                                                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Delete Modal -->
    <div x-show="confirmationDelete" class="relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                            <svg class="size-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold text-gray-900" id="modal-title">Confirm Deletion</h3>
                            <p class="mt-2 text-sm text-gray-500">Are you sure you want to delete all chats?</p>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button wire:click="deleteAllConversations" type="button"
                            class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                            Confirm Delete
                        </button>
                        <button @click="confirmationDelete = false; isModalSetting = true" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal confirm log out --}}
    <div x-show="confirmationLogout" x-trap="confirmationLogout" style="display: none;" class="relative z-50"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                            <svg class="size-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold text-gray-900" id="modal-title">Confirm Logout</h3>
                            <p class="mt-2 text-sm text-gray-500">Are you sure you want to delete all chats?</p>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                                Log out
                            </button>
                        </form>
                        <button @click="confirmationLogout = false; isModalSetting = true" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- modal for select persona --}}
    <div x-show="selectStudentPersona || {{ $mustSelectPersona ? 'true' : 'false' }}"
        x-trap="selectStudentPersona || {{ $mustSelectPersona ? 'true' : 'false' }}" style="display: none;"
        class="relative z-50" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 w-3/4 sm:p-6">
                    <main class="overflow-hidden">
                        <div class="mx-auto max-w-2xl text-center my-2">
                            <p class="text-lg font-semibold tracking-tight text-gray-900 sm:text-2xl">Students Personas
                            </p>
                        </div>
                        <ul role="list" id="personas-list" wire:model="selectedPersona" role="list"
                            class="mx-auto grid max-w-2xl grid-cols-1 gap-x-6 gap-y-20 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:gap-x-8 xl:col-span-2 overflow-y-auto max-h-[400px]">
                            @foreach ($personas as $persona)
                                <li wire:click="$set('selectedPersona', {{ $persona->id }})"
                                    class="persona-card border rounded-md m-4 p-4 cursor-pointer hover:shadow-lg 
                                           {{ $selectedPersona == $persona->id ? 'ring-4 ring-blue-500' : '' }}">
                                    <img class="aspect-[3/2] w-full rounded-2xl object-cover"
                                        src="https://images.unsplash.com/photo-1505840717430-882ce147ef2d?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=8&amp;w=1024&amp;h=1024&amp;q=80"
                                        alt="">
                                    <h3 class="mt-6 text-lg/8 font-semibold text-gray-900">{{ $persona->title }}</h3>
                                    <p class="text-base/7 text-gray-600">Senior Developer</p>
                                    <p class="mt-4 text-base/7 text-gray-600">{!! $persona->description !!}</p>
                                    <ul role="list" class="mt-6 flex gap-x-6">
                                        <li>
                                            <a href="#" class="text-gray-400 hover:text-gray-500">
                                                <span class="sr-only">X</span>
                                                <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                    aria-hidden="true">
                                                    <path
                                                        d="M11.4678 8.77491L17.2961 2H15.915L10.8543 7.88256L6.81232 2H2.15039L8.26263 10.8955L2.15039 18H3.53159L8.87581 11.7878L13.1444 18H17.8063L11.4675 8.77491H11.4678ZM9.57608 10.9738L8.95678 10.0881L4.02925 3.03974H6.15068L10.1273 8.72795L10.7466 9.61374L15.9156 17.0075H13.7942L9.57608 10.9742V10.9738Z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </main>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse space-x-2 mx-4">
                        <button @click="selectStudentPersona = false; isModalSetting = true" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white ml-2 px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                        {{-- button save user persona --}}
                        <button wire:click="savePersonaSetting"
                            @click="selectStudentPersona = false; isModalSetting = true"
                            class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Message -->
    <div x-show="toastMessage" class="relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">

                <div
                    class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6">
                    <div>
                        <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-green-100">
                            <svg class="size-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-base font-semibold text-gray-900" id="modal-title">Configuration
                                successful</h3>
                            <div class="mt-2">
                                @if (session()->has('error'))
                                    <p class="text-sm text-red-600">{{ session('error') }}
                                    </p>
                                @endif
                                @if (session()->has('success'))
                                    <p class="text-sm text-green-600">
                                        {{ session('success') }}</p>
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6">
                        <button @click="toastMessage = false; isModalSetting = false" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0">
                            Close
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
