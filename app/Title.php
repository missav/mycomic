<?php

namespace App;

class Title
{
    public static function appendAppName(string $title): string
    {
        return $title . ' - ' . localized(config('app.name'));
    }
}
