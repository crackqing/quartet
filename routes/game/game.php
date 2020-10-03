<?php 

    #提供给游戏客户端接口交互 客户端--客户端认证  绑定代理上级关系
    $router->post('orderSync','ExteriorController@orderSync');
    $router->post('cashSync','ExteriorController@cashSync');
    $router->post('bindInviteCode','ExteriorController@bindInviteCode');
    $router->post('checkBindStatus','ExteriorController@checkBindStatus');
    $router->any('loginNotice','ExteriorController@loginNotice');
    $router->any('marqueeNotice','ExteriorController@marqueeNotice');
    $router->any('server/inviteSw','ExteriorController@inviteSw');


    $router->post('server/checkBindExist','ExteriorController@checkBindExist');


    #留存，日活， 同步 ,用户    
    $router->post('userSync','ExteriorController@userSync');
    $router->post('statistics','ExteriorController@statistics');

        #VIP充值配置， 支付配置
    $router->any('vipSeting','ExteriorController@vipSeting');
    $router->any('paySeting','ExteriorController@paySeting');
    $router->any('cashSeting','ExteriorController@cashSeting');
    $router->any('qrcodeImages','ExteriorController@qrcodeImages');


       #短信接口 验证码| other
    $router->post('activetySmsCaptcha','ExteriorController@activetySmsCaptcha');

        #获取游戏大厅的数据
    $router->post('server/gameHallKindid','ExteriorController@gameHallKindid');




