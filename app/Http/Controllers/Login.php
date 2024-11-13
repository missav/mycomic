<?php

namespace App\Http\Controllers;

use App\Concerns\WithUserUuid;
use App\Recombee\Recombee;
use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\ValidationException;
use Recombee\RecommApi\Requests\MergeUsers;

class Login
{
    use ValidatesRequests, WithUserUuid;

    public function __invoke()
    {
        $data = $this->validateWith([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        $originalUserUuid = $this->getUserUuid();

        if (! auth()->attempt($data, true)) {
            throw ValidationException::withMessages([
                'password' => __('auth.failed'),
            ]);
        }

        request()->session()->regenerate();

        if ($originalUserUuid && $originalUserUuid !== user()->id) {
            try {
                Recombee::send(new MergeUsers(user()->id, $originalUserUuid, [
                    'cascadeCreate' => true,
                ]));
            } catch (Exception) {}
        }

        return $this->responseUserUuid();
    }
}
