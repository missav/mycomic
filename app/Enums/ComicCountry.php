<?php

namespace App\Enums;

enum ComicCountry: string
{
    case JAPAN = 'japan';
    case HONGKONG = 'hongkong';
    case OTHER = 'other';
    case EUROPE = 'europe';
    case CHINA = 'china';
    case KOREA = 'korea';

    public function text(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return __("enums.comic_country.{$this->value}", [], $locale);
    }
}
