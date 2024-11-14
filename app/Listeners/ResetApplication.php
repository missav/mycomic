<?php

namespace App\Listeners;

use App\LinkHeader;
use App\Seo;

class ResetApplication
{
    public function handle(): void
    {
        LinkHeader::reset();
        Seo::reset();
    }
}
