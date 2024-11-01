<?php

namespace App\Concerns;

trait WithUserUuid
{
    public ?string $userUuid = null;

    protected function getUserUuid(): ?string
    {
        if (auth()->check()) {
            return user()->id;
        }

        return request()->cookies->get('user_uuid');
    }
}
