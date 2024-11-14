<?php

namespace App;

use Illuminate\Support\Str;

class LinkHeader
{
    protected static array $items = [];

    public static function reset(): void
    {
        static::$items = [];
    }

    public static function add(string $url, array $params): void
    {
        if (! Str::startsWith($url, 'http')) {
            $url = asset($url);
        }

        static::$items[] = [
            'url' => $url,
            'params' => $params,
        ];
    }

    public static function addPreconnect(string $url): void
    {
        static::add($url, ['rel' => 'preconnect']);
    }

    public static function all(): array
    {
        if (empty(static::$items)) {
            return [];
        }

        return [
            'Link' => collect(static::$items)
                ->map(fn (array $item) => "<{$item['url']}>; ".
                    collect($item['params'])
                        ->map(fn (string $value, string $key) => "{$key}=\"{$value}\"")
                        ->implode('; ')
                )
                ->implode(', '),
        ];
    }
}
