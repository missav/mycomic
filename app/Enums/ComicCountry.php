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

    public function text(): string
    {
        return __("enums.comic_country.{$this->value}");
    }
}
