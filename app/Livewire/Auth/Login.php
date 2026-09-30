<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public function authenticate(): void
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'Those credentials do not match our records.']);
        }

        request()->session()->regenerate();
        $this->redirectIntended(route('maps.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.app');
    }
}
