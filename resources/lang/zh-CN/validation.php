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

    'accepted' => ':attribute必须接受',
    'active_url' => ':attribute必须是一个合法的 URL',
    'after' => ':attribute 必须是 :date 之后的一个日期',
    'after_or_equal' => ':attribute 必须是 :date 之后或相同的一个日期',
    'alpha' => ':attribute只能包含字母',
    'alpha_dash' => ':attribute只能包含字母、数字、中划线或下划线',
    'alpha_num' => ':attribute只能包含字母和数字',
    'array' => ':attribute必须是一个数组',
    'before' => ':attribute 必须是 :date 之前的一个日期',
    'before_or_equal' => ':attribute 必须是 :date 之前或相同的一个日期',
    'between' => [
        'numeric' => ':attribute 必须在 :min 到 :max 之间',
        'file' => ':attribute 必须在 :min 到 :max KB 之间',
        'string' => ':attribute 必须在 :min 到 :max 个字符之间',
        'array' => ':attribute 必须在 :min 到 :max 项之间',
    ],
    'boolean' => ':attribute字符必须是 true 或false, 1 或 0 ',
    'confirmed' => ':attribute 二次确认不匹配',
    'date' => ':attribute 必须是一个合法的日期',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => ':attribute 与给定的格式 :format 不符合',
    'different' => ':attribute 必须不同于 :other',
    'digits' => ':attribute必须是 :digits 位.',
    'digits_between' => ':attribute 必须在 :min 和 :max 位之间',
    'dimensions' => ':attribute具有无效的图片尺寸',
    'distinct' => ':attribute字段具有重复值',
    'email' => ':attribute必须是一个合法的电子邮件地址',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'exists' => '选定的 :attribute 是无效的.',
    'file' => ':attribute必须是一个文件',
    'filled' => ':attribute的字段是必填的',
    'gt' => [
        'numeric' => ':attribute 必须大于 :value',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => ':attribute 必须大于或等于 :value',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => ':attribute必须是 jpeg, png, bmp 或者 gif 格式的图片',
    'in' => '选定的 :attribute 是无效的',
    'in_array' => ':attribute 字段不存在于 :other',
    'integer' => ':attribute 必须是个整数',
    'ip' => ':attribute必须是一个合法的 IP 地址。',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => ':attribute必须是一个合法的 JSON 字符串',
    'lt' => [
        'numeric' => ':attribute 必须小于 :value',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => ':attribute 必须小于或等于 :value',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => ':attribute 的最大长度为 :max 位',
        'file' => ':attribute 的最大为 :max',
        'string' => ':attribute 的最大长度为 :max 字符',
        'array' => ':attribute 的最大个数为 :max 个.',
    ],
    'mimes' => ':attribute 的文件类型必须是 :values',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => ':attribute 的最小长度为 :min 位',
        'file' => ':attribute 大小至少为 :min KB',
        'string' => ':attribute 的最小长度为 :min 字符',
        'array' => ':attribute 至少有 :min 项',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value',
    'not_in' => '选定的 :attribute 是无效的',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => ':attribute 必须是数字',
    'password' => '密码错误',
    'present' => ':attribute 字段必须存在',
    'regex' => ':attribute 格式是无效的',
    'required' => ':attribute 字段是必须的',
    'required_if' => ':attribute 字段是必须的当 :other 是 :value',
    'required_unless' => ':attribute 字段是必须的，除非 :other 是在 :values 中',
    'required_with' => ':attribute 字段是必须的当 :values 是存在的',
    'required_with_all' => ':attribute 字段是必须的当 :values 是存在的',
    'required_without' => ':attribute 字段是必须的当 :values 是不存在的',
    'required_without_all' => ':attribute 字段是必须的当 没有一个 :values 是存在的',
    'same' => ':attribute和:other必须匹配',
    'size' => [
        'numeric' => ':attribute 必须是 :size 位',
        'file' => ':attribute 必须是 :size KB',
        'string' => ':attribute 必须是 :size 个字符',
        'array' => ':attribute 必须包括 :size 项',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => ':attribute 必须是一个字符串',
    'timezone' => ':attribute 必须是个有效的时区.',
    'unique' => ':attribute 已存在',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => ':attribute 无效的格式',
    'uuid' => 'The :attribute must be a valid UUID.',
    'decimal_between' => ':attribute 必须在 :min 和 :max 之间',
    'decimal_min' => ':attribute 最小为 :min',
    'decimal_max' => ':attribute 最大为 :max',
    'decimal_non_zero' => ':attribute不可为零',
    'decimal_eq' => ':attribute 必须是 :value',
    'scale_between' => ':attribute 小数位数必须在 :min 和 :max 之间',
    'scale_min' => ':attribute 小数位数最小为 :min',
    'scale_max' => ':attribute 小数位数最大为 :max',
    'google2fa' => ':attribute 无效',
    'excel' => ':attribute 的文件类型必须是 xlsx, xls',
    'bank_card' => ':attribute 无效',
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
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'bank_name' => '银行名称',
        'bank_district' => '开户省市',
        'bank_address' => '分行地址',
        'account_name' => '戶名',
        'account_number' => '银行帐号',
        'branch' => '支行名称',
        'amount' => '金额',
        'amount_confirmation' => '确认金额',
        'processing_fee' => '手续费',
        'total_amount' => '总金额',
        'username' => '姓名',
        'name' => '姓名',
        'email' => '信箱',
        'company' => '公司',
        'phone' => '电话',
        'password' => '密码',
        'password_confirmation' => '确认密码',
        'old_password' => '旧密码',
        'new_password' => '新密码',
        'new_password_confirmation' => '确认新密码',
        'bank_password' => '取款密码',
        'deposit_processing_fee_percent' => '储值手续费百分比',
        'min_deposit_amount' => '最小充值额',
        'max_deposit_amount' => '最大充值额',
        'affordable_min' => '最小负担额',
        'affordable_max' => '最大负担额',
        'payment_processing_fee' => '代付手续费',
        'min_payment_amount' => '最小代付额',
        'max_payment_amount' => '最大代付额',
        'merchant' => '商户编号',
        'merchant_id' => '商户编号',
        'sign' => '签名',
        'api_key' => 'API金钥',
        'customized_id' => '自订编号',
        'google_key' => '谷歌验證码',
        'balance' => '馀额',
        'status' => '状态',
        'boolean_index' => '布林指標',
        'additional_parameters' => '附加参数',
        'title' => '标题',
        'content' => '内容',
        'default_payment_callback_url' => '预设代付回调连结',
        'field' => '栏位',
        'order_id' => '订单编号',
        'cause' => '原因',
        'callback_url' => '回调连结',
        'api_token_switch' => 'API Token 开关'
    ],
];