<?php

namespace App\Service;

class Tool
{
    public static $bMenu = [];

    public static $bMenuList = [];

    public static $agentList = [];

    public static $agentList3 = [];

    public static $tpoParentid = [];
    /**
     * [treeAgent 查找代理下的所有用户，包括子代理绑定的情况 用于一级情况]
     * @return [type] [description]
     */
    public static function treeAgent(&$data, $pid, $level =1)
    {
        //为1则根代理，最上级的
        foreach ($data as $k => $v) {
            if ($v['bind_id'] == $pid) {
                $v['level'] = $level;
                self::$agentList[] = $v;
                //echo $level.'===>'.$v['UserID'].'===>'.$v['AgencyID'].'<br/>';
                unset($data[$k]);
        
                self::treeAgent($data, $v['email'], $level+1);
            }
        }
        return self::$agentList;
    }

    /**
     * [treeAgent2 查找代理下的所有用户，包括子代理绑定的情况  用于二级情况]
     * @return [type] [description]
     */
    public static function treeAgent2(&$data, $pid, $level =1)
    {
        //为1则根代理，最上级的
        foreach ($data as $k => $v) {
            if ($v['second_agent_id'] == $pid) {
                self::$agentList[] = $v;
                //echo $level.'===>'.$v['UserID'].'===>'.$v['AgencyID'].'<br/>';
                unset($data[$k]);
                self::treeAgent2($data, $v['agent_id'], $level+1);
            }
        }
        return self::$agentList;
    }


    /**
     * [treeAgent3 查找代理下的所有用户，包括子代理绑定的情况  用于二级情况]
     * @return [type] [description]
     */
    public static function treeAgent3(&$data, $pid, $level =1)
    {
        //为1则根代理，最上级的
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                self::$agentList3[] = $v;
                //echo $level.'===>'.$v['UserID'].'===>'.$v['AgencyID'].'<br/>';
                unset($data[$k]);
                self::treeAgent3($data, $v['id'], $level+1);
            }
        }
        return self::$agentList3;
    }
    /**
     * [rootNumber 查找所有的上级与下级用户情况]
     * @param  [type]  &$data [description]
     * @param  [type]  $pid   [description]
     * @param  integer $level [description]
     * @return [type]         [description]
     */
    public static function getTopParentid(&$data, $pid, $level=1)
    {
        foreach ($data as $k => $v) {
            if ($v['agent_id'] == $pid) {
                if ($v['is_root'] == 1) {
                    return;
                }
                $v['level'] = $level;
                self::$tpoParentid[] =$v;
                unset($data[$k]);
                self::getTopParentid($data, $v['first_agent_id'], $level +1);
            }
        }
        return self::$tpoParentid;
    }



    /**
     * [bMenu 查找菜单下所有的子菜单继续显示 ]
     * @return [type] [description]
     */
    public static function bMenu(&$data, &$tree, $pid = 0)
    {
        foreach ($data as $k => $v) {
            if ($pid == $v['pid']) {
                $tree[$v['id']] = $v;
                unset($data[$k]);
                self::bMenu($data, $tree[$v['id']]['children'], $v['id']);
            }
        }
        return $tree;
    }

    
    /**
     * [bMenu 查找菜单下所有的子菜单继续显示 ]
     * @return [type] [description]
     */
    public static function bMenuStatis(&$data, &$tree, $pid = 0)
    {
        foreach ($data as $k => $v) {
            if ($pid == $v['pid']) {
                $tree[$v['id']] = $v;

                unset($data[$k]);

                self::bMenuStatis($data, $tree[$v['id']]['children'], $v['id']);
            }
        }
        return $tree;
    }



    /**
    * @desc 返回xml格式数据
    * @param array $data 数据
    * return string
    */
    public static function xml($data = array())
    {
        header("Content-Type:text/xml");
        $xml = '';
        $xml .= "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml .= "<xml>\n";
        $xml .= self::xmlEncode($data);
        $xml .= "</xml>";
          
        echo $xml;
    }

    public static function xmlEncode($result)
    {
        $xml = $attr ='';
        foreach ($result as $key=>$val) {
            if (is_numeric($key)) {
                $attr = "id='{$key}'";
                $key = "item{$key}";
            }
            $xml .= "<{$key}>";
            $xml .= is_array($val) ? self::xmlEncode($val) : $val;
            $xml .= "</{$key}>\n";
        }
        return $xml;
    }

}
