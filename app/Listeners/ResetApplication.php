<?php

namespace App\Listeners;

use App\Seo;

class ResetApplication
{
    public function handle(): void
    {
        Seo::reset();
    }
}
