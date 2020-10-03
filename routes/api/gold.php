<?php 

#金币操作--主栏目 
$router->get('gold/vipSeting','GameManagerController@vipSeting')
            ->name('gold.vipseting');
$router->post('gold/vipSeting','GameManagerController@vipSetingUpdate')
            ->name('gold.vipSetingUpdate.skip');
$router->get('gold/paySeting','GameManagerController@paySeting')
            ->name('gold.payseting');
$router->post('gold/paySeting','GameManagerController@paySetingUpdate')
            ->name('gold.paySetingUpdate.skip');
$router->get('gold/cashSeting','GameManagerController@cashSeting')
            ->name('gold.cashseting');
$router->post('gold/cashSeting','GameManagerController@cashSetingUpdate')
            ->name('gold.cashseting.skip');            
#兑换状态--分为查询  与 总时客服银行充值 与 手动代付 接入第三方支付的代付处理




/**
 * 重复点击的次情下，会出现多次的兑换订单情况,
 * 
 * 1.需要框架限制访问次数  1分内只可以访问两次。接口
 * 
 * 2.前端签名验证
 */
// $router->middleware('throttle:6,1')->group(function ($router) {
    $router->post('gold/cashStatus','GameManagerController@cashStatus')
                    ->name('operate.cash.analysis.post');
// });

#支付平台操作--检测余额 显示对应的商户号
$router->get('gold/balance/query','GameManagerController@checkShopMoney')
                     ->name('operate.cash.query.skip');


#更新赠送的配置与开关
$router->get('gold/activeGive','GameManagerController@activeGive')
                ->name('gold.activeGive');
$router->post('gold/activeGive','GameManagerController@activeGiveUpdate')
                ->name('gold.activeGive');



#风控管理
$router->get('gold/WindControl','GameManagerController@WindControl')
                ->name('gold.WindControl');


