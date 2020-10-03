<?php 

        #人员管理 用户的(游戏记录,赠送记录,游戏充值 ,游戏赠送,==接口显示)
        $router->get('personnel/user', 'PersonnelController@user')
                        ->name('personnel.user');
        $router->get('personnel/user/gameRecord', 'PersonnelController@userGameRecord')
                        ->name('personnel.userGameRecord');
        $router->get('personnel/user/gameRecordTime', 'PersonnelController@userGameRecordTime')
                        ->name('personnel.userGameRecordTime.skip');


        $router->get('personnel/user/gameCashs', 'PersonnelController@gameCashs')
                        ->name('personnel.gameCashs');
        $router->get('personnel/user/gameOrders', 'PersonnelController@gameOrders')
                        ->name('personnel.gameOrders');
        $router->get('personnel/user/gameGive', 'PersonnelController@gameGive')
                        ->name('personnel.gameGive');

        $router->post('personnel/user/gamePay', 'PersonnelController@gamePay')
                        ->name('personnel.gamePay');
        $router->post('personnel/user/gamePayGive', 'PersonnelController@gamePayGive')
                        ->name('personnel.gamePayGive');

        $router->post('personnel/user/userInfoEdit', 'PersonnelController@userInfoEdit')
                        ->name('personnel.userInfoEdit');
        $router->post('personnel/user/lock', 'PersonnelController@userLock')
                        ->name('personnel.userLock');
        #兑换绑定更新 远程服务器
        $router->get('personnel/user/getCashInfo','PersonnelController@userCashBind')
                        ->name('personnel.userCashInfo');
        $router->post('personnel/user/getCashInfo','PersonnelController@userCashBindUpdate')
                        ->name('personnel.userCashInfo');
        #金币扣除
        $router->get('personnel/user/goldDeduct','PersonnelController@goldDeductGet')
                        ->name('personnel.goldDeduct');
        $router->post('personnel/user/goldDeduct','PersonnelController@goldDeduct')
                        ->name('personnel.goldDeduct.post');



        #人员管理--代理信息 充值报表,兑换报表,赠送记录. 游戏记录 ，上级修改.
        $router->get('personnel/agent', 'PersonnelController@agent')
                        ->name('personnel.agent');
        $router->get('personnel/agent/pay','PersonnelController@agentPay')
                        ->name('personnel.agentPay');
        $router->get('personnel/agent/cashs','PersonnelController@agentCashs')
                        ->name('personnel.agentcashs');
        $router->get('personnel/agent/give','PersonnelController@agentGive')
                        ->name('personnel.agentgive');
        $router->get('personnel/agent/Record','PersonnelController@agentRecord')
                        ->name('personnel.agentRecord');
        $router->get('personnel/agent/edit','PersonnelController@agentEdit')
                        ->name('personnel.agentedit');
        $router->post('personnel/agent/edit','PersonnelController@agentEditOp')
                        ->name('personnel.agentedit');
        $router->get('personnel/agent/platform','PersonnelController@agentGameTotal')
                        ->name('personnel.agentplatform.skip');

        #真实操作分 查看与操作
        $router->post('personnel/agent/platform','PersonnelController@agentGameTotalEdit')
                        ->name('personnel.agentplatform.skip');
        $router->get('personnel/getUserinfo','PersonnelController@getUserinfo')
                        ->name('personnel.getUserInfo.skip');

        $router->get('personnel/agent/total','PersonnelController@agentTotal')
                        ->name('personnel.agentTotal.skip');
        $router->get('personnel/agent/qrcode','PersonnelController@agentQrcode')
                        ->name('personnel.agentQrocde.skip');
        $router->get('personnel/agent/qrcode','PersonnelController@agentQrcode')
                        ->name('personnel.agentQrocde.skip');

        #根邀请码--开关设置
        $router->get('personnel/agent/inviteSw','PersonnelController@inviteCodeSw')
                        ->name('personnel.inviteCodeSw');

        $router->post('personnel/agent/inviteSw','PersonnelController@inviteCodeSwEdit')
                        ->name('personnel.inviteCodeSwEdit.skip');
        #查询当前充值时的点击流水
        $router->get('personnel/user/chargeWater','PersonnelController@chargeWater')
                        ->name('personnel.user.chargewater.skip');
        #查询当前充值时的点击流水
        $router->get('personnel/user/giftWater','PersonnelController@giftWater')
                        ->name('personnel.user.giftwater.skip');
