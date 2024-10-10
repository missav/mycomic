<?php

if (! function_exists('cdn')) {
    function cdn(?string $path = ''): string
    {
        if (\Illuminate\Support\Str::startsWith($path, 'http')) {
            return $path;
        }

        $path = $path ? \Illuminate\Support\Str::start($path, '/') : $path;

        return config('app.cdn_url') . $path;
    }
}
