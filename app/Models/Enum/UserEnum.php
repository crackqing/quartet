<?php
namespace App\Models\Enum;

class UserEnum
{
    //状态类别
    const INVALID=-1 ;//已删除
    const NORMAL=0; //正常
    const FREEZE= 1; //冻结

    const LOGINERROR=10; //登录错误
    const MANAGER=1; //管理员判断,用户表与管理表使用一张表

    public static function getStatusName($status)
    {
        switch ($status){
            case self::INVALID:
                return '已删除';
            case self::NORMAL:
                return '正常';
            case self::FREEZE:
                return '冻结';
            case self::LOGINERROR:
                return '登录错误';
            case self::MANAGER:
                return '登录不是管理帐号';
            default:
                    return '正常';
        }
    }
}