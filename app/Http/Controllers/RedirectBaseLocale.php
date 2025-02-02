<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class RedirectBaseLocale
{
    public function __invoke(): RedirectResponse
    {
        return redirect(Str::replace('/cn', '', request()->getRequestUri()));
    }
}
