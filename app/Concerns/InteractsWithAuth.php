<?php

namespace App\Concerns;

trait InteractsWithAuth
{
    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public function register(): void
    {
        
    }
}
