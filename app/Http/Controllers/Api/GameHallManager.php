<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Server\GameSettings;

class GameHallManager extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $kindid = $request->kindid ?? false;
        $gameSettings = GameSettings::orderBy('tid','ASC');
        if ($kindid && $kindid != 10000) {
                $gameSettings = $gameSettings->where('kindid',$kindid);
        }
        $gameSettings = $gameSettings->get()->toArray();
        foreach ($gameSettings as $k => $v) {
           $gameSettings[$k]['enterLimit'] = $v['enterLimit'] / 10000;
           $gameSettings[$k]['minCannon'] = $v['minCannon'] / 10000;
           $gameSettings[$k]['maxCannon'] = $v['maxCannon'] / 10000;
        }
        return $this->success($gameSettings);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * 每种kindid 游戏毕需从一开始,tid自动配置 查找上个自动加1
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $kindid = $request->kindid;
        //押减得----
        $data =[
            'name'  => $request->name ?? '桌子名称',
            'enterLimit'  => $request->enterLimit ?  $request->enterLimit * 10000 : 0,
            'minCannon'  => $request->minCannon ? $request->minCannon * 10000 : 100 ,
            'maxCannon'  => $request->minCannon ? $request->minCannon * 10 * 10000: 100 * 10,
            'tax'  => $request->tax ?? 2,
            'difficulty'  => $request->difficulty ?? 4,
            'type'  => $request->type ?? 2,
            'kindid'  => $request->kindid ?? '桌子名称',
            'enable'  => $request->enable ?? 0,
            'tableType'  => $request->tableType ?? 0,
            'extend_1'  => $request->extend_1 ?? 0,
        ];
        //桌子
        if ($data['kindid'] == 1005) {
            $data['maxCannon'] = $request->minCannon * 50000;
        }
        #查找上个tid
        $kindidTid = GameSettings::where('kindid',$kindid)
                        ->orderBy('tid','DESC')
                        ->value('tid');

        if ($kindidTid == 0) {
           $data['tid'] = 1;
        } else {
            $data['tid'] = $kindidTid + 1;
        } 
        if (GameSettings::create($data)) {
            #获取指定的kindid,批量推送到游戏中
            if ($kindid) {
                $datagame = GameSettings::where('kindid',$kindid)
                                ->get()
                                ->toArray();
                $this->api->configGame('config',$kindid,$datagame);
            }
            return  $this->success('success');
        }
        return $this->failed('failed');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $gameSettings = GameSettings::where('kindid', $id)->get();

        return $this->success($gameSettings);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $gameSettings = GameSettings::where('id', $id)->first();
        if ($gameSettings->kindid != 1100  && $gameSettings->kindid != 1005) {
            $gameSettings->minCannon = null;
            $gameSettings->maxCannon = null;
            $gameSettings->difficulty = null;
            $gameSettings->type =null;
        }  else {
            $gameSettings->enterLimit = $gameSettings->enterLimit /10000;
            $gameSettings->minCannon = $gameSettings->minCannon / 10000;
            $gameSettings->maxCannon = $gameSettings->kindid  == 1005 ? 
                                             $gameSettings->maxCannon /50000:
                                             $gameSettings->maxCannon /10000;

            if ($gameSettings->kindid == 1005) {
                $gameSettings->difficulty = null;
            }
            if ($gameSettings->kindid == 1005) {
                $gameSettings->tax = null;
            }

        }
        return $this->success($gameSettings);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $kindid = $request->kindid;
        //押减得----
        $data =[
            'name'  => $request->name ?? '桌子名称',
            'enterLimit'  => $request->enterLimit ?  $request->enterLimit * 10000 : 0,
            'minCannon'  => $request->minCannon ? $request->minCannon * 10000 : 100 ,
            'maxCannon'  => $request->minCannon ? $request->minCannon * 10 * 10000: 100 * 10,
            'tax'  => $request->tax ?? 2,
            'difficulty'  => $request->difficulty ?? 4,
            'type'  => $request->type ?? 2,
            'kindid'  => $request->kindid ?? '桌子名称',
            'enable'  => $request->enable ?? 0,
            'tableType'  => $request->tableType ?? 0,
            'extend_1'  => $request->extend_1 ?? 0,
        ];
        //桌子
        if ($data['kindid'] == 1005) {
            $data['maxCannon'] = $request->minCannon * 50000;
        }

        $gameSettings = GameSettings::where('id', $id)->update($data);
        #获取指定的kindid,批量推送到游戏中
        if ($kindid) {
            $datagame = GameSettings::where('kindid',$kindid)->get()->toArray();
            $this->api->configGame('config',$kindid,$datagame);
        }
        return $this->success($gameSettings);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return false;
    }
    /* 游戏大厅管理 各种操作处理  gameHalls/ */


    /**
     * 获取桌子的状态 function
     *
     * @param Request $request
     * @return void
     */
    public function fetchstatus(Request $request)
    {
        $kindid = $request->kindid ?? '';
        $tid = $request->tid ?? '';

        if ($kindid && $tid) {
            $result = $this->api->configGame('fetchstatus', $kindid, ['tid' => $tid],true);

            return $result != 'failed' ?
                 $this->success( json_decode($result) ) :
                 $this->internalError('远程服务调用失败,请稍后重试!');
        }
        return  $this->failed('参数传入有误');
    }
    /**
     * 游戏的桌子操作处理 function
     *
     * @return void
     */
    public function operate(Request $request)
    {
        $operate = $request->operate ?? '';
        $kindid = $request->kindid ?? '';
        $tid = $request->tid ?? '';
        $num = $request->num ?? '';
        $status = $request->status ?? '';

        if (!$kindid) {
            return  $this->failed('参数传入有误');
        }
        switch ($operate) {
            case 'start': // 开启游戏
                $result =  $this->api->configGame('start', $kindid);

                return $result != 'failed' ?
                    $this->success('success') :
                    $this->internalError('远程服务调用失败,请稍后重试!');
                break;
            case 'stop': // 关闭游戏
                $result =  $this->api->configGame('stop', $kindid);

                return $result != 'failed' ?
                    $this->success('success') :
                    $this->internalError('远程服务调用失败,请稍后重试!');

                break;
            case 'reset': // 重置桌子
                if (!$tid) {
                    return  $this->failed('参数传入有误');
                }
                $result =  $this->api->configGame('reset', $kindid, ['tid'=>$tid]);

                return $result != 'failed' ?
                    $this->success('success') :
                    $this->internalError('远程服务调用失败,请稍后重试!');

                break;
            case 'clear': // 清0桌子
                if (!$tid) {
                    return  $this->failed('参数传入有误');
                }
                $result =  $this->api->configGame('clear', $kindid, ['tid'=>$tid]);

                return $result != 'failed' ?
                    $this->success('success') :
                    $this->internalError('远程服务调用失败,请稍后重试!');

                break;
            case 'startrobot': // 开启机器人
                if (!$num) {
                    return  $this->failed('参数传入有误');
                }
                $result =  $this->api->configGame('startrobot', $kindid, ['num'=>$num]);
                return $result != 'failed' ?
                    $this->success('success') :
                    $this->internalError('远程服务调用失败,请稍后重试!');
                break;
            case 'stoprobot': // 关闭机器人
                $result =  $this->api->configGame('stoprobot', $kindid);
                return $result != 'failed' ?
                    $this->success('success') :
                    $this->internalError('远程服务调用失败,请稍后重试!');
                break;
            case 'tablelist':  //open closed
                $result =  $this->api->configGame('tablelist', $kindid);
                return $result != 'failed' ?
                    $this->success(json_decode($result)) :
                    $this->internalError('远程服务调用失败,请稍后重试!');
                break;
            case 'placeHolder': //占位情况处理
                $result =  $this->api->configGame('placeHolder', $kindid,['tid'=>$tid,'status'=> $status]);
                return $result != 'failed' ?
                    $this->success(json_decode($result)) :
                    $this->internalError('远程服务调用失败,请稍后重试!');
                break;
                
            default:
                # code...
                break;
        }
    }
}
