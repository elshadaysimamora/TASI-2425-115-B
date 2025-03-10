<?php

use App\Livewire\Pages\Chat;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/chat', function () {
//         $chat = auth()->user()->conversations()->create([
//             'model' => 'gpt-3.5-turbo-0125',
//         ]);
//         return redirect()->route('chat.show', $chat);
//     })->name('chat');
//     Route::get('/chat/{conversation:uuid}', Chat::class)->name('chat.show');
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });

// Rute untuk guest (belum login)
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

// Rute yang memerlukan autentikasi
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Redirect setelah login ke dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Route chat
    Route::get('/chat', function () {
        $chat = auth()->user()->conversations()->create([
            'model' => 'gpt-3.5-turbo-0125'
        ]);
        return redirect()->route('chat.show', $chat);
    })->name('chat');

    Route::get('/chat/{conversation:uuid}', Chat::class)->name('chat.show');

    // Tambahkan route '/' untuk user yang sudah login
    Route::get('/', function () {
        return redirect()->route('chat');
    });
});
