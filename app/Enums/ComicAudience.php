<?php

namespace App\Enums;

enum ComicAudience: string
{
    case SHAONV = 'shaonv';
    case SHAONIAN = 'shaonian';
    case QINGNIAN = 'qingnian';
    case ERTONG = 'ertong';
    case TONGYONG = 'tongyong';

    public function text(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return __("enums.comic_audience.{$this->value}", [], $locale);
    }
}
