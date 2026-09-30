<?php

use App\Http\Controllers\MapController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Maps\Editor;
use App\Livewire\Maps\Index;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/maps');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/maps', Index::class)->name('maps.index');
    Route::get('/maps/{map}', Editor::class)->name('maps.show');
    Route::put('/maps/{map}/atlas', [MapController::class, 'save'])->name('maps.save');
    Route::post('/maps/{map}/invitations', [MapController::class, 'invite'])->name('maps.invite');
    Route::get('/invitations/{token}', [MapController::class, 'accept'])->name('invitations.accept');
    Route::post('/logout', [MapController::class, 'logout'])->name('logout');
});
