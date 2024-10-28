<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

trait InteractsWithAuth
{
    use WithUserUuid;

    public bool $isLoggedIn = false;

    public ?string $actionAfterLogin = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $this->dispatch('modal-close');
        $data = $this->validate([
            'actionAfterLogin' => ['nullable', Rule::in($this->availableActionsAfterLogin)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        auth()->login(User::create([
            'id' => $this->userUuid ?? Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]), true);

        $this->isLoggedIn = true;
        $this->dispatch('modal-close');

        if ($this->actionAfterLogin) {
            $this->{$this->actionAfterLogin}();
        }
    }
}
