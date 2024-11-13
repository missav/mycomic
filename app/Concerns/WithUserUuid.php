<?php

namespace App\Concerns;

trait WithUserUuid
{
    protected function getUserUuid(): ?string
    {
        return user()?->id ?? request()->cookies->get('user_uuid');
    }

    protected function responseUserUuid(array $data = []): array
    {
        return array_merge(['userUuid' => $this->getUserUuid()], $data);
    }
}
