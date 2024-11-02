<?php

namespace App\Concerns;

trait WithUserUuid
{
    public ?string $userUuid = null;

    protected function getUserUuid(): ?string
    {
        return user()?->id ??
            $this->userUuid ??
            request()->cookies->get('user_uuid');
    }
}
