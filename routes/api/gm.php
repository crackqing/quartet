<?php
$router->get('gm/notice', 'GameManagerController@notice')
            ->name('gm.notice');
$router->put('gm/notice/gameLoginNotice', 'GameManagerController@gameLoginNotice')
            ->name('gm.gameLoginNotice');
$router->put('gm/notice/agentLoginNotice', 'GameManagerController@agentLoginNotice')
            ->name('gm.agentLoginNotice');
$router->put('gm/notice/marqueeLoginNotice', 'GameManagerController@marqueeLoginNotice')
            ->name('gm.marqueeLoginNotice');
$router->get('gm/gameRecord', 'GameManagerController@index')
            ->name('gm.gameRecord');
$router->get('gm/gameRecordTotal', 'GameManagerController@gameRecordTotal')
            ->name('gm.gameRecord');
$router->get('gm/gameRecordCharts', 'GameManagerController@gameRecordCharts')
            ->name('gm.gameRecordCharts.skip');
$router->get('gm/test', 'GameManagerController@testApp')
            ->name('gm.testApp');

#更换图片处理--images 新增图片images
$router->any('gm/images','GameManagerController@images')
            ->name('gm.images');

$router->get('gm/FangKa/gameRecordDetail','GameManagerController@gameFangKaRecordDetail')
            ->name('gm.gameFangKaRecordDetail');


#游戏大厅管理--各种类型的操作
$router->resource('gm/gameHall','GameHallManager');
$router->get('gm/gameHalls/fetchstatus','GameHallManager@fetchstatus')
            ->name('gameHalls.fetchstatus');
$router->get('gm/gameHalls/operate','GameHallManager@operate')
            ->name('gameHalls.operate');

#游戏管理---邮件管理 游戏里面消息通知
$router->resource('gm/notify','MessageController');

#游戏管理--记录详情
$router->get('gm/gameRecordDetail','GameManagerController@gameRecordDetail')
                ->name('gm.gameRecordDetail');


