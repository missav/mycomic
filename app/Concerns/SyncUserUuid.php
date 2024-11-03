<?php

namespace App\Concerns;

trait SyncUserUuid
{
    public function syncUserUuid(): void
    {
        if (user()) {
            $this->dispatch('user-uuid-updated', userUuid: user()->id);
        }
    }
}
