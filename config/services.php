<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],
    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    #阿里短信 服务 有阿里大于
    'ali_sms'   => [
        'accessKeyId'   => env('ALI_SMS_ACCESSKEYID'),
        'accessKeySecret'   => env('ALI_SMS_ACCESSKEYSECRET')
    ],
    #游戏服务的接口 本地访问127.0.0.1:5555 同一服务部署
    'game_server'   => [
            'url'   => env('GAME_SERVER_URL'),
    ],

    #认证地址 passport:install 安装证书
    'oauth_api' =>  env('OAUTH_API'),

    #兑换平台的 台子1 的商户ID 与密钥. 与本项目的回调地址
    'payment'   => [
        'taizi' =>[
            'app_id'    => env('PAYMENT_APP_ID'),
            'app_secret'    => env('PAYMENT_APP_SECRET'),
            'payment'   => env('PAYMENT_URL_BANKENTRY'),
            'payBalance'    => env('PAYMENT_URL_QUERYBALANCE'),
            'notiyurl'  => env('PAYMENT_URL_NOTIFYURL'),
            'third' => env('PAYMEN_THIRD_PAYHANK'),
        ]
    ],
    #安卓与苹果的下载地址更换
    'platform'  => [
        'ios'   => env('PLATFORM_IOS'), 
        'android'   => env('PLATFORM_ANDROID'), 
    ],
    #二维码的生成地址更换,这样游戏前端请求的图片就是新的
    'platform_str'  => '?version=11111',

    'weixin'   => [
        'client_id' => env('WECHAT_APP_ID'),
        'client_secret' => env('WECHAT_SECRET'),
        'redirect'   => env('WECHAT_REDIRECT_URI')
    ]
];
