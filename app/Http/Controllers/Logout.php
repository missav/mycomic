<?php

namespace App\Http\Controllers;

use App\Concerns\WithUserUuid;

class Logout
{
    use WithUserUuid;

    public function __invoke(): array
    {
        auth()->logout();

        request()->session()->regenerate();

        return $this->responseUserUuid();
    }
}
