<?php

namespace App\Recombee;

use Illuminate\Support\Facades\Facade;
use Recombee\RecommApi\Client;

class Recombee extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}
