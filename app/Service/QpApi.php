<?php

namespace App\Service;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class QpApi
{
    protected $baseUrl;
    protected $client;

    //游戏记录 与 增加金币 获取用户信息与绑定信息处理接口
    protected $getGameRecordDetail = 'getGameRecordDetail.php?';
    protected $addCoins = 'addCoins.php?';
    protected $getUserInfo = 'getUserInfo.php?';
    protected $bindUser = 'bindUser.php?';


    // type=player所有玩家，type=limitplayer指定玩家
    protected $getCoins = 'getCoins.php?';
    protected $addSysMessage = 'addSysMessage.php?';
    
    #兑换信息与更换
    protected $getUserWithdrawalInfo = 'getUserWithdrawalInfo.php?';
    protected $modifyUserInfo = 'modifyUserInfo.php?';

    protected $withdrawal_record = 'withdrawal_record.php?';

    /* 游戏大厅管理 开启关闭 重置 清零 开启BOT 关闭BOT 获取桌子信息 配置规则 */
    protected $configGame = 'configGame.php?';

    #设置用户消息
    protected $addUsrMessage = 'addUsrMessage.php?';


    public function __construct()
    {
        $this->baseUrl = Config::get('services.game_server.url');

        $this->client = new Client();
    }
    /**
     * 获取游戏分析的数据,每日进行统计 function
     *
     * @param integer $startidx
     * @return void
     */
    public function getGameRecordDetail(Int $startidx)
    {
        $param = [
            'startidx'  => $startidx
        ];
        return  $this->guzzleClient($this->getGameRecordDetail, $param, true);
    }
    /**
     * 赠送金币 function
     *  id,uid,manager_id,coins,type,remark,created_at,updated_at
     * @param [用户ID] ] $uid
     * @param [金币，为负的情况只有11其它 负数的话报错 ] $coins
     * @param [type 6=赠送，7=充值,11平台扣除] $type
     * @return void
     */
    public function addCoins($uid, $coins, $type = 7)
    {
        $param = [
            'uid'   => $uid,
            'coins'   => $coins,
            'type'   => $type,
        ];
        return  $this->guzzleClient($this->addCoins, $param, true);
    }
    /**
     * 查询用户信息 function
     *
     * @param boolean $uid
     * @return void
     */
    public function getUserInfo($uid = false)
    {
        $param = [
            'uid'   => $uid
        ];
        return  $this->guzzleClient($this->getUserInfo, $param, true);
    }
    /**
     * 封号与解封 操作 method unBindUser bindUser function
     *
     * @param boolean $uid
     * @return void
     */
    public function bindUser($uid = false, $method = 'bindUser')
    {
        $param = [
            'uid'   => $uid,
            'method'   => $method,
        ];
        return  $this->guzzleClient($this->bindUser, $param);
    }
    /**
     * 获取玩家金币总量 操作 type function
     *
     * @param boolean $uid array []
     * @return void
     */
    public function getCoins($uid = false, $type = 'player', $players = '')
    {
        $param = [
            'type'  => $type,
            'players'   => $players ?? 'false'
        ];
        return  $this->guzzleClient($this->getCoins, $param, true);
    }

    /**
     * 游戏公告的更换 function
     *
     * @param [type] $msg
     * @return void
     */
    public function addSysMessage($msg)
    {
        $param = [
            'msg'   => $msg
        ];
        return  $this->guzzleClient($this->addSysMessage, $param);
    }
    /**
     * 兑换绑定获取  function
     *
     * @param [type] $uid
     * @return void
     */
    public function getUserWithdrawalInfo($uid)
    {
        $param = [
            'uid'   => $uid
        ];
        return $this->guzzleClient($this->getUserWithdrawalInfo, $param, true);
    }



    /**
     * 兑换绑定更新 修改密码成功 修改手机成功 function
     *
     * @param [type] $uid
     * @param [type] $param
     * @return void
     */
    public function modifyUserInfo($uid, $param, $method = '')
    {
        $data =[
            'uid' => $uid,
            'method' => $method,
            'param' => json_encode($param),
        ];
        //参数错误：只支持支付宝和银行卡
        return $this->guzzleClient($this->modifyUserInfo, $data);
    }
    /**
     * 银行兑换状态变更 function
     */
    public function withdrawal_record($exchangeid, $state)
    {
        $data = [
            'exchangeid'    => $exchangeid,
            'state'    => $state,
        ];
        return  $this->guzzleClient($this->withdrawal_record, $data);
    }
    /**
     * 发送消息 function
     *
     * @param [int] $uid  0是所有用户 用户ID
     * @param [int] $type  0充值消息，1兑换消息 ，2为系统公告消息
     * @param [str] $msg 消息内容
     * @return void
     */
    public function addUsrMessage($uid,$type,$msg)
    {
        $data = [
            'uid'   => $uid,
            'type'   => $type,
            'msg'   => $msg,
        ];
        return $this->guzzleClient($this->addUsrMessage,$data);
    }

    /**
     * 游戏大厅管理 function
     *
     * @param [type] $method 接口操作类型--参考文档
     * @param [type] $kindid 游戏ID
     * @param [type] $param json_encode 传入
     * @return string
     */
    public function configGame($method, $kindid, $param = '')
    {
        $data = [
            'method'    => $method,
            'kindid'    => $kindid,
            'param'    => $param ? json_encode($param) : '',
        ];
        return  $this->guzzleClient($this->configGame, $data);
    }
    /**
     * Guzzle的操作 function
     */
    public function guzzleClient($requstUrl, $param = [], $decode = false)
    {
        $url = $this->baseUrl . $requstUrl;
        $sign = ['sign' => 'd58e3582afa99040e27b92b13c8f2280'];

        $paramMerge = array_merge($param, $sign);
        $response = $this->client->get($url, [
            'query' => $paramMerge
        ]);

        $body = $response->getBody();
        $content = $body->getContents();

        \Log::info($requstUrl, ['content'=>$content,'paramMerge'=>$paramMerge,'status' => $response->getStatusCode()]);
        #检测不能返回-1与 failed 否则默认为失败处理
        if ($response->getStatusCode() == 200 
            && $content != '未知错误' 
            && $content != -1) {
            return $decode == true ? json_decode($content, true) :  $content;
        }

        return 'failed';
    }
}
