<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\User;
use Cache;
use App\Models\Server\GameSettings;

/**
 * 条件搜索: 发送给前端的参数说明 class
 */
class SearchController extends BaseController
{
    /**
     * 返回的游戏 对应ID function
     *
     * @return void
     */
    public function index()
    {
        $data = [
            '9' => '对战牛牛',
            '1000'  => '百人牛牛',
            '1001'  => '奔驰宝马',
            '1002'  => '飞禽走兽',
            // '1003'  => '比大小',
            '1004'  => '百家乐',
            '1005'  => '水浒传',
            '1015'  => '红黑大战',
            '1100'  => '摇钱树',
            '10000' => '所有'
        ];

        return $this->success($data);
    }
    /**
     * 返回管理为1的用户,缓存时间为60 function
     *
     * @return void
     */
    public function member()
    {
        $id = $this->userInfoBase()->email;

        $users = Cache::remember('quartet:user_managerBind', 60, function () use ($id) {
            return User::select('id','email')->where('manager',1)->where('bind_id',$id)->get()->toArray();
        });
        #所有，默认就是为空 判断两个 增加 登录进来的帐号 直属与非直属的 搜索
        $data =[
            1   => [
                'id'    => 10000,
                'email' => '直属用户'
            ],
            2   => [
                'id'    => 20000,
                'email' => '非直属用户'
            ]
        ];
        return $this->success(array_merge($data,$users));
    }


    /**
     * 游戏_桌子显示 function
     *
     * @return void
     */
    public function kindidTid($kindid = '')
    {
        #应该获取当前用户的kindid 返回对应桌子状态
        $data = GameSettings::select('tid','name')->get()->toArray();

        // $data = [
        //     '1' => '练习厅',
        //     '2' => '香港厅',
        //     '3' => '澳门厅',
        //     '4' => '台湾厅',
        //     '5' => '纽约厅'
        // ];
        return $this->success($data);
    }


}
