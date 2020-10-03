<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Service\QpApi;

/**
 *游戏调控处理 class
 */
class GameController extends CommonController
{
    public function index()
    {
        $data = [
            '1000'  => '百人',
        ];
        return view('Admin.game.index', compact('data'));
    }

    /**
     * 游戏控制 格式游戏类型调控 function
     *
     * @param Request $request
     * @return void
     */
    public function gameControl(Request $request, QpApi $api)
    {
        $kindid = $request->kindid ?? 'false';
        $rate = $request->rate ?? 60;
        if (!isset($kindid) &&  !isset($rate)) {
            return back()->with('error', '参数不能为空');
        }
        $data = [
            'tid' => 1,
            'rate' => $rate,
        ];
        $result = $api->configGame('controlRate', $kindid, $data);

        if ($result != 'failed') {
            return back()->with('status', '操作成功');
        }
        return back()->with('error', '接口调用失败');
    }
}
