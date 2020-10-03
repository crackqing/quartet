<?php 


$router->get('operateGameAnalysis', 'OperateController@index')
            ->name('operate.game.analysis');
$router->get('payAnalysis', 'OperateController@payAnalysis')
            ->name('operate.pay.analysis');
$router->get('cashAnalysis', 'OperateController@cashAnalysis')
            ->name('operate.cash.analysis');
$router->get('giveAnalysis','OperateController@giveAnalysis')
            ->name('operate.give.analysis');
$router->get('dailyBillsAnalysis','OperateController@dailyBillsAnalysis')
            ->name('operate.dailyBillsAnalysis.analysis');
$router->get('activeGiveAnalysis','OperateController@activeGiveData')
            ->name('operate.active.analysis');
$router->post('cashStatus', 'OperateController@cashStatus')
            ->name('operate.cash.status');





#日活,留存,新增等操作

$router->get('operate/statis','StatisController@operate')
            ->name('operate.statis.analysis');

$router->get('statis/new', 'StatisController@new')
            ->name('operate.statis.new');
$router->get('statis/remain', 'StatisController@remain')
            ->name('operate.statis.remain');
$router->get('statis/online', 'StatisController@online')
            ->name('operate.statis.online');            