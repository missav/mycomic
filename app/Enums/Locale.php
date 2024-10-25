<?php

namespace App\Enums;

enum Locale: string
{
     case ZH = 'zh';
     case CN = 'cn';

    public function label(): string
    {
        return match($this->value) {
            'zh' => '繁體中文',
            'cn' => '简体中文',
        };
    }

    public function code(): string
    {
        return match($this->value) {
            'zh' => 'zh-Hant',
            'cn' => 'zh-Hans',
        };
    }

    public static function current(): Locale
    {
        return Locale::from(app()->getLocale());
    }
}
