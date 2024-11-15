<?php

namespace App\Http\Controllers;

use App\Concerns\WithUserUuid;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Register
{
    use WithUserUuid;

    public function __invoke(): array
    {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        auth()->login(User::create([
            'id' => $this->getUserUuid() ?? Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]), true);

        return $this->responseUserUuid();
    }
}
