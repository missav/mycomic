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

if (! function_exists('origin')) {
    function origin(?string $path = ''): string
    {
        if (\Illuminate\Support\Str::startsWith($path, 'http')) {
            return $path;
        }

        $path = $path ? \Illuminate\Support\Str::start($path, '/') : $path;

        return config('app.origin_url') . $path;
    }
}

if (! function_exists('localizedRoute')) {
    function localizedRoute(
        string|\App\Enums\Locale $name,
        array $parameters = [],
        bool $absolute = true,
        \App\Enums\Locale $locale = null,
    ): string
    {
        $locale = $locale ? $locale->value : app()->getLocale();

        if ($name instanceof \App\Enums\Locale) {
            $name = str_replace(app()->getLocale(), $name->value, request()->route()->getName());

            return route($name, array_merge(request()->route()->parameters(), request()->all()), $absolute);
        }

        return route("{$locale}.{$name}", $parameters, $absolute);
    }
}

if (! function_exists('localized')) {
    function localized(?string $text = null): string
    {
        return app()->getLocale() === 'zh' ? zh($text) : cn($text);
    }
}

if (! function_exists('zh')) {
    function zh(?string $text = null): string
    {
        if (! $text) {
            return '';
        }

        return \Tiacx\ChineseConverter::convert($text, 's2t');
    }
}

if (! function_exists('cn')) {
    function cn(?string $text = null): string
    {
        if (! $text) {
            return '';
        }

        return \Tiacx\ChineseConverter::convert($text, 't2s');
    }
}

if (! function_exists('user')) {
    function user(): ?\App\Models\User
    {
        return auth()->user();
    }
}
