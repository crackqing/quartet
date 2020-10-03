<?php
use Illuminate\Support\Facades\Cache;
use App\Service\Tool;
use App\UserSimple;
use Illuminate\Support\Facades\DB;
use App\User;

/**
 * 通用函数处理
 */
if (!function_exists('base64Auto')) {
    /**
     * [base64Auto 判断是否base64自动解码或者直接输入]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    function base64Auto($value)
    {
        return $value === base64_encode(base64_decode($value)) ? base64_decode($value)  : $value ;
    }
}

if (!function_exists('kindid2game')) {
    /**
     * 返回对应的游戏中文说明 function
     *
     * @return string
     */
    function kindid2game($kindid)
    {
        $data = [
            '10000' => '所有'
        ];
        return $data[$kindid] ?? '未知游戏';
    }
}



if (! function_exists('url_safe_base64_encode')) {
    function url_safe_base64_encode($data)
    {
        return str_replace(array('+','/', '='), array('-','_', ''), base64_encode($data));
    }
}

if (! function_exists('url_safe_base64_decode')) {
    function url_safe_base64_decode($data)
    {
        $base_64 = str_replace(array('-','_'), array('+','/'), $data);
        return base64_decode($base_64);
    }
}




if (!function_exists('user_relation')) {
    /**
     * [user_relation 获取用户的代理关系,缓存用户关系]
     * @param  integer $parent_id [UserID]
     * @param  boolean $format    [格式化数据]
     * @param  boolean $whereInData    [返回wherein查询封装的数组]
     * @return [mixed]             [description]
     */
    function user_relation($parent_id = 1, $format = false, $whereInData = false)
    {
        $users = Cache::remember('quartet_user_all', 60, function () {
            return User::select('id', 'email', 'bind_id', 'agent_id', 'manager')
                            ->get()
                            ->toArray();
        });
        $data = Tool::treeAgent($users, $parent_id);
        Tool::$agentList = [];
        #删除level为1的是直属，其它的为非直属.
        foreach ($data as $k => $v) {
            if ($v['level'] == 1 || $v['manager'] == 1) {
                unset($data[$k]);
            }
        }
        $whereIn = '';
        if ($format && $whereInData) {
            foreach ($data as $k => $v) {
                $whereIn .= $v['agent_id'].',';
            }
            return trim($whereIn, ',') ;
        }
        if ($format) {
            $number = count($data);
            $IsAgency = 0;
            foreach ($data as $k => $v) {
                if ($v['is_agent'] == 1) {
                    $IsAgency++;
                }
            }
            return '总共:'.$number.'其中代理为:'.$IsAgency .'人';
        }
        return $data;
    }
}



if (!function_exists('user_relation_level')) {
    /**
     * [user_relation_level 获取用户的代理关系,缓存用户关系]
     * @param  integer $parent_id [UserID]
     * @param  boolean $format    [格式化数据]
     * @param  boolean $whereInData    [返回wherein查询封装的数组]
     * @return [mixed]             [description]
     */
    function user_relation_level($parent_id = 1, $format = false, $whereInData = false)
    {
        $users = Cache::remember('quartet_user_all', 60, function () {
            return User::select('id', 'email', 'bind_id', 'agent_id', 'manager')
                            ->get()
                            ->toArray();
        });

        $data = Tool::treeAgent($users, $parent_id);
        Tool::$agentList = [];

        #删除level为1的是直属，其它的为非直属.
        foreach ($data as $k => $v) {
            if ($v['manager'] != 1) {
                unset($data[$k]);
            }
        }
        $whereIn = '';
        if ($format && $whereInData) {
            foreach ($data as $k => $v) {
                $whereIn .= $v['agent_id'].',';
            }
            return trim($whereIn, ',') ;
        }

        if ($format) {
            $number = count($data);
            $IsAgency = 0;
            foreach ($data as $k => $v) {
                if ($v['is_agent'] == 1) {
                    $IsAgency++;
                }
            }
            return '总共:'.$number.'其中代理为:'.$IsAgency .'人';
        }
        return $data;
    }
}

if (!function_exists('user_relation_pasre')) {
    /**
     * [user_relation_pasre 获取用户的代理关系,缓存用户关系]
     * @param  integer $parent_id [UserID]
     * @param  boolean $format    [格式化数据]
     * @param  boolean $whereInData    [返回wherein查询封装的数组]
     * @return [mixed]             [description]
     */
    function user_relation_pasre($parent_id = 1)
    {
        $users = Cache::remember('quartet_user_all', 60, function () {
            return User::select('id', 'email', 'bind_id', 'agent_id', 'manager')
                            ->get()
                            ->toArray();
        });
        $data = Tool::treeAgent($users, $parent_id);
        Tool::$agentList = [];

        $str = '';
        foreach ($data as $k => $v) {
            if (empty($v['email'])) {
                continue ;
            }
            if ($v['level'] != 1) {
                continue;
            }
            $str .= $v['email'].',';
        }
        return rtrim($str, ',') ;
    }
}

if (!function_exists('user_relation_all')) {
    /**
     * [user_relation_all 读取单个代理]
     * @param  integer $parent_id [UserID]
     * @return [mixed]             [description]
     */
    function user_relation_all($parent_id = 1)
    {
        $users = Cache::remember('quartet_user_all', 60, function () {
            return User::select('id', 'email', 'bind_id', 'agent_id', 'manager')
                            ->get()
                            ->toArray();
        });
        $data = Tool::treeAgent($users, $parent_id);
        Tool::$agentList = [];

        return $data;
    }   
}


if (!function_exists('key_word_search')) {
    /**
     * 关键字搜索 指定对应字段搜索 多条件搜索 function
     *
     * @param [orm 数据模型] $model
     * @param array $keywordArray orm下的每个不同的字段处理.
     * @param array $keyword 传进来的关键字.
     * @return void
     */
    function key_word_search($model, $keywordArray = [], $keyword)
    {
        if (is_array($keywordArray)) {
            foreach ($keywordArray as $k  => $v) {
                $model->orWhere($v, 'like', '%'.$keyword.'%');
            }
            return $model;
        } else {
            return $model;
        }
    }
}
if (!function_exists('member_search')) {
    /**
     * 用户搜索 （对应后台登录用户的关系搜索） function
     *
     * @param [orm 数据模型] $model
     * @param array $member 可以是文字也可以是单个ID
     * @param array $keyword 传进来的关键字.
     * @return void
     */
    function member_search($model, $keyword, $member = 'agent_id')
    {
        if ($keyword) {
            #当前登录的用户直属关系与非.
            $id = \Auth::guard('api')->id();
            $user =  UserSimple::where('id', $id)->first();

            if ($keyword == '直属用户') {
                $paraentId = $user->getAgentNumberIdAttribute();
                if (empty($paraentId)) {
                    return $model;
                }
                return $model->whereRaw(DB::raw("$member IN ($paraentId)"));
            } elseif ($keyword == '非直属用户') {
                $paraentId = $user->getPlayerNumberIdAttribute();

                if (empty($paraentId)) {
                    return $model;
                }
                    
                return $model->whereRaw(DB::raw("$member IN ($paraentId)"));
            } else {
                $paraentId = $user->getAgentNumberIdAttribute($keyword);
                if (empty($paraentId)) {
                    return $model->where($member, $keyword);
                }
                return  $model->whereRaw(DB::raw("$member IN ($paraentId)"));
            }
        } else {
            return $model;
        }
    }
}
if (!function_exists('time_str')) {
    /**
     * 根据时间文字生成对应的时间区间 用yesterday来统一查询
     *
     * @return void
     */
    function time_str($timeStr)
    {
        $data = [];
        $data['today']  = date('Y-m-d 00:00:00');

        switch ($timeStr) {
            case '昨日':
                $data['yesterday'] = date('Y-m-d 00:00:00',strtotime('-1 day'));
                break;
            case '今日':
                $data['yesterday'] = date('Y-m-d 00:00:00');
                $data['today'] = date('Y-m-d 00:00:00',strtotime('+1 day'));
                break;
            case '本周':
                $data['yesterday'] = date('Y-m-d 00:00:00');
                $data['today'] = date('Y-m-d 00:00:00',strtotime('+7 day'));
                break;
            case '累计':
                $data = [];
                break;
            default:
                $data = [];
                break;
        }
        return $data;
        
    }
}

if(!function_exists('agent_daily')){
    /**
     * 每周的直属代理计算处理. function
     *
     * @param [type] $parameters
     * @return void
     */
    function agent_daily($performance)
    {
        if ($performance < 10000) {
            return ['玩家',200];
        } elseif ($performance >= 10001 && $performance < 50000){
            return ['会员',160];
        } elseif ($performance >= 50001 && $performance < 100000){
            return ['资深会员',170];
        } elseif ($performance >= 100001 && $performance < 250000){
            return ['代理',180];
        } elseif ($performance >= 250001 && $performance < 500000){
            return ['超级代理',190];
        } elseif ($performance >= 500001 && $performance < 1000000){
            return ['总代理',200];
        } elseif ($performance >= 1000001 && $performance < 2000000){
            return ['超级总代理',210];
        } elseif ($performance >= 2000001 && $performance < 4000000){
            return ['总监级',220];
        } elseif ($performance >= 4000001 && $performance < 7000000){
            return ['股东级',230];
        } elseif ($performance >= 7000001 && $performance < 10000000){
            return ['超级股东',240];
        } elseif ($performance > 10000001) {
            return ['董事长',250];
        }
        return false;
    }
}
if(!function_exists('agent_daily_percentage')){
    /**
     * 个人的计算 抽水 * 20 * 比率 得出今天应该返的多少值. function
     *
     * @param [type] $parameters
     * @return void
     */
    function agent_daily_percentage($performance)
    {
        if ($performance < 9999) {
            return ['初级代理',0.006];
        } elseif ($performance >= 10000 && $performance < 19999){
            return ['中级代理',0.007];
        } elseif ($performance >= 20000 && $performance < 49999){
            return ['高级代理',0.008];
        } elseif ($performance >= 50000 && $performance < 99999){
            return ['资深代理',0.010];
        } elseif ($performance >= 100000 && $performance < 299999){
            return ['超级代理',0.012];
        } elseif ($performance >= 300000 && $performance < 499999){
            return ['总代理',0.014];
        } elseif ($performance >= 500000 && $performance < 799999){
            return ['超级总代理',0.016];
        } elseif ($performance >= 800000 && $performance < 999999){
            return ['总监级',0.018];
        } elseif ($performance >= 1000000 && $performance < 1999999){
            return ['股东级',0.020];
        } elseif ($performance >= 2000000){
            return ['超级股东',0.022];
        }
        return false;
    }
}

if (!function_exists('check_current_manager')) {
    /**
     * 检测当前登录用户的直属玩家数量 function
     *
     * @return void
     */
    function check_current_manager()
    {
        $id = \Auth::guard('api')->id();
        if ($id == 1) {
            return false;
        }
        //查找当前直属玩家的情况
        $userSimple = \App\UserSimple::where('id',$id)->first();


        return $userSimple->getAgentNumberIdAttribute($userSimple->email);
    }
}

if(!function_exists('str_encrypt_sign')){
    //新版加密
    function str_encrypt_sign($parameters)
    {
        unset($parameters['sign']);

        $parameters['key']='334444DDEREQQQQQQQQQQQQQQEREWDSF4545';
        ksort($parameters);
        $signPars = url_build_sign($parameters);
        $signPars=trim($signPars,'&');
        return strtolower(md5($signPars));
    }

};
if(!function_exists('url_build_sign')){
    
    /**
     * url签名生成 function
     *
     * @param [type] $parameters
     * @return void
     */
    function url_build_sign($parameters)
    {
        $signPars = '';
        foreach ($parameters as $k => $v) {
            if (isset($v)) {
                $signPars .= $k . '=' . $v . '&';
            }
        }
        return $signPars;
    }

}

