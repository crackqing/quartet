<?php 
#登录检测---是否绑定后台的代理帐号 5位 1分内只能登录10次操作
$router->post('client/login','ClientController@login')->middleware('throttle:10,1');
Route::group(['prefix' => '/client/','middleware' => 'auth:client'],function($router){
    #1-4 数据   数字对应着需要图 原两个代理后台的调整
    $router->get('personal','ClientController@personal');
    #5-8 数据
    $router->get('team','ClientController@team');
    #个人玩家实时流水
    $router->get('personalCurrentWater','ClientController@personalCurrentWater');
    #昨日个人业绩详情
    $router->get('yesterdayPersonalCommission','ClientController@yesterdayPersonalCommission');
    #昨日团队业绩详情
    $router->get('yesterTeamResults','ClientController@yesterTeamResults');
    #昨日团队佣金详情
    $router->get('yesterdayTeamCommission','ClientController@yesterdayTeamCommission');
});
