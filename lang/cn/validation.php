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

    'accepted'        => '必须接受:attribute。',
    'active_url'      => ':attribute并非一个有效的网址。',
    'after'           => ':attribute必须要晚于 :date。',
    'after_or_equal'  => ':attribute必须要等于 :date 或更晚。',
    'alpha'           => ':attribute只能以字母组成。',
    'alpha_dash'      => ':attribute只能以字母、数字、连接线(-)及底线(_)组成。',
    'alpha_num'       => ':attribute只能以字母及数字组成。',
    'array'           => ':attribute必须为阵列。',
    'before'          => ':attribute必须要早于 :date。',
    'before_or_equal' => ':attribute必须要等于 :date 或更早。',
    'between'         => [
        'numeric' => ':attribute必须介乎 :min 至 :max 之间。',
        'file'    => ':attribute必须介乎 :min 至 :max KB 之间。 ',
        'string'  => ':attribute必须介乎 :min 至 :max 个字符之间。',
        'array'   => ':attribute: 必须有 :min 至 :max 个项目。',
    ],
    'boolean'        => ':attribute必须是布尔值。',
    'confirmed'      => ':attribute确认栏位的输入并不相符。',
    'date'           => ':attribute并非一个有效的日期。',
    'date_equals'    => ':attribute必须等于 :date。',
    'date_format'    => ':attribute与 :format 格式不相符。',
    'different'      => ':attribute与 :other 必须不同。',
    'digits'         => ':attribute必须是 :digits 位数字。',
    'digits_between' => ':attribute必须介乎 :min 至 :max 位数字。',
    'dimensions'     => ':attribute图片尺寸不正确。',
    'distinct'       => ':attribute已经存在。',
    'email'          => ':attribute必须是有效的电邮地址。',
    'ends_with'      => ':attribute结尾必须包含下列之一：:values',
    'exists'         => ':attribute不存在。',
    'file'           => ':attribute必须是文件。',
    'filled'         => ':attribute不能留空。',
    'gt'             => [
        'numeric' => ':attribute必须大于 :value。',
        'file'    => ':attribute必须大于 :value KB。',
        'string'  => ':attribute必须多于 :value 个字符。',
        'array'   => ':attribute必须多于 :value 个项目。',
    ],
    'gte' => [
        'numeric' => ':attribute必须大于或等于 :value。',
        'file'    => ':attribute必须大于或等于 :value KB。',
        'string'  => ':attribute必须多于或等于 :value 个字符。',
        'array'   => ':attribute必须多于或等于 :value 个项目。',
    ],
    'image'    => ':attribute必须是一张图片。',
    'in'       => '所拣选的 :attribute选项无效。',
    'in_array' => ':attribute没有在 :other 中。',
    'integer'  => ':attribute必须是一个整数。',
    'ip'       => ':attribute必须是一个有效的 IP 地址。',
    'ipv4'     => ':attribute必须是一个有效的 IPv4 地址。',
    'ipv6'     => ':attribute必须是一个有效的 IPv6 地址。',
    'json'     => ':attribute必须是正确的 JSON 格式。',
    'lt'       => [
        'numeric' => ':attribute必须小于 :value。',
        'file'    => ':attribute必须小于 :value KB。',
        'string'  => ':attribute必须少于 :value 个字符。',
        'array'   => ':attribute必须少于 :value 个项目。',
    ],
    'lte' => [
        'numeric' => ':attribute必须小于或等于 :value。',
        'file'    => ':attribute必须小于或等于 :value KB。',
        'string'  => ':attribute必须少于或等于 :value 个字符。',
        'array'   => ':attribute必须少于或等于 :value 个项目。',
    ],
    'max' => [
        'numeric' => ':attribute不能大于 :max。',
        'file'    => ':attribute不能大于 :max KB。',
        'string'  => ':attribute不能多于 :max 个字符。',
        'array'   => ':attribute不能多于 :max 个项目。',
    ],
    'mimes'     => ':attribute必须为 :values 的档案。',
    'mimetypes' => ':attribute必须为 :values 的档案。',
    'min'       => [
        'numeric' => ':attribute不能小于 :min。',
        'file'    => ':attribute不能小于 :min KB。',
        'string'  => ':attribute不能小于 :min 个字符。',
        'array'   => ':attribute不能小于 :min 个项目。',
    ],
    'not_in'               => '所拣选的 :attribute选项无效。',
    'not_regex'            => ':attribute的格式错误。',
    'numeric'              => ':attribute必须为一个数字。',
    'password'             => '密码错误',
    'present'              => ':attribute必须存在。',
    'regex'                => ':attribute的格式错误。',
    'required'             => ':attribute不能留空。',
    'required_if'          => '当 :other 是 :value 时 :attribute不能留空。',
    'required_unless'      => '当 :other 不是 :values 时 :attribute不能留空。',
    'required_with'        => '当 :values 出现时 :attribute不能留空。',
    'required_with_all'    => '当 :values 出现时 :attribute不能留空。',
    'required_without'     => '当 :values 留空时 :attributefield 不能留空。',
    'required_without_all' => '当 :values 都不出现时 :attribute不能留空。',
    'same'                 => ':attribute与 :other 必须相同。',
    'size'                 => [
        'numeric' => ':attribute的大小必须是 :size。',
        'file'    => ':attribute的大小必须是 :size KB。',
        'string'  => ':attribute必须是 :size 个字符。',
        'array'   => ':attribute必须是 :size 个单元。',
    ],
    'starts_with' => ':attribute开头必须包含下列之一：:values',
    'string'      => ':attribute必须是一个字符串',
    'timezone'    => ':attribute必须是一个正确的时区值。',
    'unique'      => ':attribute已经存在。',
    'uploaded'    => ':attribute上传失败。',
    'url'         => ':attribute的格式错误。',
    'uuid'        => ':attribute必须是有效的 UUID。',

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
        'email' => '电邮',
        'password' => '密码',
        'password_confirmation' => '确认密码',
        'rating' => '评分',
    ],
];
