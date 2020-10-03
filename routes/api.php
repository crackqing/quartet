<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// $api = app('Dingo\Api\Routing\Router');

// /**
//  * 四方v1.0 DEMO Api
//  *      1.外部接口: 充值订单同步 | 兑换同步. 同步原始数据,增加其它字段.
//  *          1.1 提供代理绑定 与关系链接口查询.  与HT差不多，返回统一就行. stateCode
//  *          
//  * 
//  *      2.新增,日活,留存 只算总的. 实时在线与HT一致每5分(crontab)查询一次. 
//  *        2.1 游戏分析: 定时groupBy计算单表数据
//  *        2.2 每日账单 定时凌晨生成，查找所有关联的表数据 汇总起来.
//  * 
//  * 
//  */

// $api->version('v1',['namespace'=>'Api'] ,function($api){
    
// });