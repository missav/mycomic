<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgetPassword
{
    public function __invoke()
    {
        $credentials = request()->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($credentials);

        if (! in_array($status, [Password::RESET_LINK_SENT, Password::INVALID_USER])) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        return response()->json([
            'result' => 'ok',
        ]);
    }
}
