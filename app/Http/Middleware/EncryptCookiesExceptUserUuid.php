<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies;

class EncryptCookiesExceptUserUuid extends EncryptCookies
{
    protected $except = [
        'user_uuid',
    ];
}
