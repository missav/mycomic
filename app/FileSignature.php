<?php

namespace App;

class FileSignature
{
    public static function append(string $path): string
    {
        $hash = str($path)->reverse()->pipe('md5')->substr(0, 6);

        return str($path)->replaceLast('.', "-{$hash}.");
    }
}
