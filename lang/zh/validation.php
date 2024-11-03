<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'        => '必須接受:attribute。',
    'active_url'      => ':attribute並非一個有效的網址。',
    'after'           => ':attribute必須要晚於 :date。',
    'after_or_equal'  => ':attribute必須要等於 :date 或更晚。',
    'alpha'           => ':attribute只能以字母組成。',
    'alpha_dash'      => ':attribute只能以字母、數字、連接線(-)及底線(_)組成。',
    'alpha_num'       => ':attribute只能以字母及數字組成。',
    'array'           => ':attribute必須為陣列。',
    'before'          => ':attribute必須要早於 :date。',
    'before_or_equal' => ':attribute必須要等於 :date 或更早。',
    'between'         => [
        'numeric' => ':attribute必須介乎 :min 至 :max 之間。',
        'file'    => ':attribute必須介乎 :min 至 :max KB 之間。 ',
        'string'  => ':attribute必須介乎 :min 至 :max 個字符之間。',
        'array'   => ':attribute: 必須有 :min 至 :max 個項目。',
    ],
    'boolean'        => ':attribute必須是布爾值。',
    'confirmed'      => ':attribute確認欄位的輸入並不相符。',
    'date'           => ':attribute並非一個有效的日期。',
    'date_equals'    => ':attribute必須等於 :date。',
    'date_format'    => ':attribute與 :format 格式不相符。',
    'different'      => ':attribute與 :other 必須不同。',
    'digits'         => ':attribute必須是 :digits 位數字。',
    'digits_between' => ':attribute必須介乎 :min 至 :max 位數字。',
    'dimensions'     => ':attribute圖片尺寸不正確。',
    'distinct'       => ':attribute已經存在。',
    'email'          => ':attribute必須是有效的電郵地址。',
    'ends_with'      => ':attribute結尾必須包含下列之一：:values',
    'exists'         => ':attribute不存在。',
    'file'           => ':attribute必須是文件。',
    'filled'         => ':attribute不能留空。',
    'gt'             => [
        'numeric' => ':attribute必須大於 :value。',
        'file'    => ':attribute必須大於 :value KB。',
        'string'  => ':attribute必須多於 :value 個字符。',
        'array'   => ':attribute必須多於 :value 個項目。',
    ],
    'gte' => [
        'numeric' => ':attribute必須大於或等於 :value。',
        'file'    => ':attribute必須大於或等於 :value KB。',
        'string'  => ':attribute必須多於或等於 :value 個字符。',
        'array'   => ':attribute必須多於或等於 :value 個項目。',
    ],
    'image'    => ':attribute必須是一張圖片。',
    'in'       => '所揀選的 :attribute選項無效。',
    'in_array' => ':attribute沒有在 :other 中。',
    'integer'  => ':attribute必須是一個整數。',
    'ip'       => ':attribute必須是一個有效的 IP 地址。',
    'ipv4'     => ':attribute必須是一個有效的 IPv4 地址。',
    'ipv6'     => ':attribute必須是一個有效的 IPv6 地址。',
    'json'     => ':attribute必須是正確的 JSON 格式。',
    'lt'       => [
        'numeric' => ':attribute必須小於 :value。',
        'file'    => ':attribute必須小於 :value KB。',
        'string'  => ':attribute必須少於 :value 個字符。',
        'array'   => ':attribute必須少於 :value 個項目。',
    ],
    'lte' => [
        'numeric' => ':attribute必須小於或等於 :value。',
        'file'    => ':attribute必須小於或等於 :value KB。',
        'string'  => ':attribute必須少於或等於 :value 個字符。',
        'array'   => ':attribute必須少於或等於 :value 個項目。',
    ],
    'max' => [
        'numeric' => ':attribute不能大於 :max。',
        'file'    => ':attribute不能大於 :max KB。',
        'string'  => ':attribute不能多於 :max 個字符。',
        'array'   => ':attribute不能多於 :max 個項目。',
    ],
    'mimes'     => ':attribute必須為 :values 的檔案。',
    'mimetypes' => ':attribute必須為 :values 的檔案。',
    'min'       => [
        'numeric' => ':attribute不能小於 :min。',
        'file'    => ':attribute不能小於 :min KB。',
        'string'  => ':attribute不能小於 :min 個字符。',
        'array'   => ':attribute不能小於 :min 個項目。',
    ],
    'not_in'               => '所揀選的 :attribute選項無效。',
    'not_regex'            => ':attribute的格式錯誤。',
    'numeric'              => ':attribute必須為一個數字。',
    'password'             => '密碼錯誤',
    'present'              => ':attribute必須存在。',
    'regex'                => ':attribute的格式錯誤。',
    'required'             => ':attribute不能留空。',
    'required_if'          => '當 :other 是 :value 時 :attribute不能留空。',
    'required_unless'      => '當 :other 不是 :values 時 :attribute不能留空。',
    'required_with'        => '當 :values 出現時 :attribute不能留空。',
    'required_with_all'    => '當 :values 出現時 :attribute不能留空。',
    'required_without'     => '當 :values 留空時 :attributefield 不能留空。',
    'required_without_all' => '當 :values 都不出現時 :attribute不能留空。',
    'same'                 => ':attribute與 :other 必須相同。',
    'size'                 => [
        'numeric' => ':attribute的大小必須是 :size。',
        'file'    => ':attribute的大小必須是 :size KB。',
        'string'  => ':attribute必須是 :size 個字符。',
        'array'   => ':attribute必須是 :size 個單元。',
    ],
    'starts_with' => ':attribute開頭必須包含下列之一：:values',
    'string'      => ':attribute必須是一個字符串',
    'timezone'    => ':attribute必須是一個正確的時區值。',
    'unique'      => ':attribute已經存在。',
    'uploaded'    => ':attribute上傳失敗。',
    'url'         => ':attribute的格式錯誤。',
    'uuid'        => ':attribute必須是有效的 UUID。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => '電郵',
        'password' => '密碼',
        'password_confirmation' => '確認密碼',
        'rating' => '評分',
    ],
];
