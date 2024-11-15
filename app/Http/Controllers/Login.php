<?php

namespace App\Http\Controllers;

use App\Concerns\WithUserUuid;
use App\Models\Record;
use App\Recombee\Recombee;
use Exception;
use Illuminate\Validation\ValidationException;
use Recombee\RecommApi\Requests\MergeUsers;

class Login
{
    use WithUserUuid;

    public function __invoke()
    {
        $credentials = request()->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        $originalUserUuid = $this->getUserUuid();

        if (! auth()->attempt($credentials, true)) {
            throw ValidationException::withMessages([
                'password' => __('auth.failed'),
            ]);
        }

        request()->session()->regenerate();

        if ($originalUserUuid && $originalUserUuid !== user()->id) {
            Record::whereUserId($originalUserUuid)->update(['user_id' => user()->id]);

            try {
                Recombee::send(new MergeUsers(user()->id, $originalUserUuid, [
                    'cascadeCreate' => true,
                ]));
            } catch (Exception) {}
        }

        return $this->responseUserUuid();
    }
}
