<?php

namespace App\Http\Controllers;

use App\Concerns\WithUserUuid;
use App\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Register
{
    use ValidatesRequests, WithUserUuid;

    public function __invoke(): array
    {
        $data = $this->validateWith([
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
