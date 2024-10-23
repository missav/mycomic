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

if (! function_exists('localizedRoute')) {
    function localizedRoute(string|\App\Enums\Locale $name, array $parameters = [], bool $absolute = true): string
    {
        $locale = app()->getLocale();

        if ($name instanceof \App\Enums\Locale) {
            $name = str_replace(app()->getLocale(), $name->value, request()->route()->getName());

            return route($name, array_merge(request()->route()->parameters(), request()->all()), $absolute);
        }

        return route("{$locale}.{$name}", $parameters, $absolute);
    }
}
