<?php

        #kindid 游戏说明 与桌子说明 搜索说明
        $router->get('search/kindid', 'SearchController@index')
                        ->name('search.kindid.skip');
        $router->get('search/member', 'SearchController@member')
                        ->name('search.member.skip');
        $router->get('search/kindidTid', 'SearchController@kindidTid')
                        ->name('search.kindidTid.skip');
