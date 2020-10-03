<?php 

#主菜单权限设置不用skip跳过  与 是否显示对应界面。而不是实际权限操作
$router->get('main/operationData','UserController@operationData')
                ->name('main.operation');

$router->get('main/personnel','UserController@personnel')
                ->name('main.personnel');

#游戏管理--下的界面显示处理用于前端配合,其它要自己手动添加
$router->get('main/gold','UserController@gold')
                ->name('main.gold');
$router->get('main/gold/gameHallShow','UserController@gameHallShow')
                ->name('gameHallShow.show');



$router->get('main/game','UserController@game')
                ->name('main.game');

#系统管理主菜单     
$router->get('main/system','UserController@system')
                ->name('main.system');