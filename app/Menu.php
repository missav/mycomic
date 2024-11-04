<?php

namespace App;

class Menu
{
    public static function main(): array
    {
        return [
            [
                'icon' => 'book-open',
                'route' => 'comics.index',
                'text' => 'Comic database',
            ],
            [
                'icon' => 'chart-bar',
                'route' => 'rank',
                'text' => 'Ranking',
            ],
            [
                'icon' => 'bookmark',
                'route' => 'bookmarks.index',
                'text' => 'My bookmarks',
            ],
            [
                'icon' => 'clock',
                'route' => 'records.index',
                'text' => 'History',
            ],
        ];
    }
}
