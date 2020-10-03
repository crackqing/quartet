<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Session;
use App\Models\Game\RecordDetail;
use DB,Config;
use Illuminate\Support\Carbon;
use App\UserSimple;
use Intervention\Image\ImageManagerStatic as Image;

class CommonController extends Controller
{


    public function todayPerformance($id)
    {
        $today = date('Y-m-d 00:00:00',time());
        $tomorrow = date('Y-m-d 00:00:00',strtotime('+1 day'));

        $record = RecordDetail::select(
            DB::raw('SUM(choushui) as choushui')
            )
            ->whereRaw(DB::raw("uid IN ($id)"))
            ->whereBetween('time', [$today,$tomorrow])
            ->get();
        return $record->sum('choushui') * 20;
    }

    public function weekPerformance($id)
    {
        $carbon = Carbon::now();
        $startWeek = $carbon->startOfWeek()->format('Y-m-d H:i');
        $endWeek =$carbon->endOfWeek()->format('Y-m-d H:i');

        $record = RecordDetail::select(
            DB::raw('SUM(choushui) as choushui')
            )
            ->whereRaw(DB::raw("uid IN ($id)"))
            ->whereBetween('time', [$startWeek,$endWeek])
            ->get();
        return $record->sum('choushui') * 20;
    }

    /**
     * 上周1 至 周日 function
     *
     * @param [type] $id
     * @return void
     */
    public function lasgWeekPerformance($id)
    {
        $lastWeek = Carbon::parse('last week');
        $lastStartWeek = $lastWeek->startOfWeek()->format('Y-m-d H:i');
        $lastEndWeek =$lastWeek->endOfWeek()->format('Y-m-d H:i');

        $lastRecord = RecordDetail::select(
            DB::raw('SUM(choushui) as choushui')
            )
            ->whereRaw(DB::raw("uid IN ($id)"))
            ->whereBetween('time', [$lastStartWeek,$lastEndWeek])
            ->get();
        return $lastRecord->sum('choushui') * 20;
    }

    /**
     * 登录的ID user -> id function
     *
     * @return void
     */
    public function authIdEmail()
    {
        $user = session('system_front_user');
        return $user->email;
    }

    /**
     * datatables的中文解析 function
     *
     * @return void
     */
    public function datatablesChinese()
    {
                return      '{
            "sProcessing":   "处理中...",
            "sLengthMenu":   "显示 _MENU_ 项结果",
            "sZeroRecords":  "没有匹配结果",
            "sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
            "sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项",
            "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
            "sInfoPostFix":  "",
            "sSearch":       "搜索:",
            "sUrl":          "",
            "sEmptyTable":     "表中数据为空",
            "sLoadingRecords": "载入中...",
            "sInfoThousands":  ",",
            "oPaginate": {
                "sFirst":    "首页",
                "sPrevious": "上页",
                "sNext":     "下页",
                "sLast":     "末页"
            },
            "oAria": {
                "sSortAscending":  ": 以升序排列此列",
                "sSortDescending": ": 以降序排列此列"
            }
        }';

    }

    /**
     * 获取玩家直属数据显示 function
     *
     * @return void
     */
    public function GetAgentNumberId()
    {
        $user = UserSimple::find(session('system_front_id'));
        $agentNumber = $user->getAgentNumberIdAttribute();

        if (!empty($agentNumber)) {
            return $agentNumber; 
        }
        return session('system_front_id');
    }


    public function getAgentNumberAttribute()
    {
        $user = UserSimple::find(session('system_front_id'));

        $agentNumber = $user->getAgentNumberAttribute();

        if (!empty($agentNumber)) {
            return $agentNumber;
        }
        return session('system_front_id'); 
    }


    /**
     * 获取玩家的直属的代理玩家--可以登录后台的 function
     *
     * @return void
     */
    public function getAgentDirectly()
    {
        $user = UserSimple::find(session('system_front_id'));
        $directly = $user->getAgentDirectlyIdAttribute();

        if (isset($directly)) {
            return $directly;
        }
        return $this->authIdEmail();         
    }

    public function getUserSimple()
    {
        return  UserSimple::find(session('system_front_id'));
    }


    /**
     * 二维码 生成  function
     *
     * @return void
     */
    public function qrcodeGeneric()
    {
        $public = public_path('qrcode');
        $projectName = Config::get('app.name');
        $projectUrl = 'https://ttpro.club';

        $user = UserSimple::find(session('system_front_id'));
        $versionDeploy = '/version__'.str_random(10);
        $userImage = $public.'/'."$user->email".'.png';
        if (!file_exists($public.'/'."$user->email".'.png')) {
            $encode = base64_encode($user->email);
            \QrCode::format('png')
            ->errorCorrection('H')
            // ->color(255,0,255)
            ->margin(0)
            ->size(250)
            ->generate($projectUrl.'/share/'.$projectName.'/'.$encode . $versionDeploy,$userImage);    
        }
        if (!file_exists($public.'/'."$user->email".'_share.png')) {

            if (Config::get('app.name') == 'tiantian') {
                $share = public_path('share/share_qrcode_tiantian.jpg');
                $img =  Image::make($share);
                 $img->insert($userImage,'bottom',0,245);

            } else {
                $share = public_path('share/jg_agent.jpg');
                $img =  Image::make($share);
                $img->insert($userImage,'bottom',100,180);
            }
 
            $img->save(public_path("qrcode/$user->email"."_share.png"));
        }
    }


}
