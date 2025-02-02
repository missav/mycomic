<?php

namespace App\Enums;

enum Locale: string
{
    case CN = 'cn';
    case ZH = 'zh';

    public function label(): string
    {
        return match($this->value) {
            'cn' => '简体中文',
            'zh' => '繁體中文',
        };
    }

    public function code(): string
    {
        return match($this->value) {
            'cn' => 'zh-Hans',
            'zh' => 'zh-Hant',
        };
    }

    public function flagUrl(): string
    {
        return cdn('img/flags/' . $this->value . '.png');
    }

    public static function current(): Locale
    {
        return Locale::from(app()->getLocale());
    }
}
