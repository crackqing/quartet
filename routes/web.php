<?php
#game share url ,support ios and Android
Route::get('share/{type?}/{id?}/{version?}','NotifyController@shareUrl');
#payNotifyHandle
Route::any('notify/{result?}', 'NotifyController@notify');

Route::any('test','NotifyController@test');

Route::group(['prefix'=> '/api/v1/','namespace'=>'Api','middleware'=> 'cors'], function ($router) {
    $router->post('authorizations', 'LoginController@login')
                ->name('login')
                ->middleware('throttle:10,1');
    $router->put('authorizations/current', 'LoginController@update')
                ->name('login.update');
        #第三方微信登录支持.演示用于绑定关联手机，或者直接登录。需要自己改. 外部调用都限制限流
    $router->post('socials/{social_type}/authorizations','LoginController@socialStore')
                ->name('api.socials.authorizations.store');
        #手机验证码发送,调用前先获取验证码,输入错误重新获取验证码.
    $router->post('verificationCodes','VerificationCodesController@store')
                ->name('api.verificationCodes.store')
                ->middleware('throttle:3,1');
    $router->post('captchas','CaptchasController@store')
                ->name('api.captchas.store');

    /***********游戏客户端接口,涉及同步订单，兑换。 支付配置 绑定************/
        require(__DIR__.'/game/game.php');
    /***********游戏客户端接口,涉及同步订单，兑换。 支付配置 绑定************/

    #auth:api (passport自带的权限验证)　check.permission (权限判断)　operation.record(操作记录)
    Route::group(['middleware'=> ['auth:api','check.permission','operation.record']], function ($router) {
        $router->delete('authorizations/current', 'LoginController@destroy')
                    ->name('login.destroy');
        /**************************operation************************/
        #运营数据(充值,赠送,兑换,每日,游戏数据报表)
        require(__DIR__.'/api/operate.php');
        #人员管理
        require(__DIR__.'/api/personnel.php');
        #金币操作
        require(__DIR__.'/api/gold.php');
        #游戏管理
        require(__DIR__.'/api/gm.php');
        #搜索共用条件
        require(__DIR__.'/api/search.php');
        #用于前端的显示界面,隐藏等操作处理
        require(__DIR__.'/api/front.php');
        /**************************operation************************/

        /***************************mobile******************************/
        require(__DIR__.'/api/mobile.php');
        /***************************mobile******************************/

        /*********************Core(Menu，RBAC,Notify)************/
        require(__DIR__.'/api/rbac.php');
        /*********************Core(Menu，RBAC,Notify)************/
    });
});

