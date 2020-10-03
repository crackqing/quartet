<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\User;
use App\Permission;

use App\Models\Server\Operation;
use App\Models\Server\ActiveLogin;
use App\Http\Resources\Admin\User as UserCollection;
use App\Http\Requests\Admin\UserRequest;

use Illuminate\Support\Facades\Hash;

use App\Http\Resources\Admin\UserArray;

use App\Service\Tool;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        #只显示管理的列表,manager 为0的是用户列表 不能登录后台操作.
        $user = User::where('manager', 1)->where('id','!=',1);

        return new UserCollection($user->with('roles')->paginate($paginate));
    }
    /**
     * 用户附加对应的角色处理. function
     *
     * @return void
     */
    public function user2Role(Request $request)
    {
        $user_id = $request->user_id ?? '';
        $role_id = $request->role_id ?? '';

        if (empty($role_id) && empty($user_id)) {
            return $this->message('角色与权限不能为空');
        }
        $user = User::findOrFail($user_id);
        $role_id = explode(',', rtrim($role_id, ',')) ;
        
        $user->detachRoles();

        foreach ($role_id as $v) {
            $user->attachRole($v);
        }

        return $this->success('更新用户角色成功');
    }
    /**
     * 登录进来的用户信息 function
     *
     * @param Request $request
     * @return void
     */
    public function userInfo(Request $request)
    {
        return $this->success($this->userInfoBase());
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {   
        $data = [
            'name'  => $request->name ?? 'default',
            'email' => $request->email,
            'password'  => Hash::make($request->password),
            'manager'   => 1,
            'bind_id'    => $request->superior? $request->superior : 80000, //默认绑定的ID为80000的上级固定死
            'amount'    => 0,
        ];
        $user = User::create($data);
        return $user ? $this->success($user) : $this->notFond('添加失败') ;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return  new UserCollection(User::where('id', $id)->get()->load('roles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 支持单一修改
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if ($id == 1) {
            return $this->failed('failed');
        }

        if ($request->name) {
            $user = User::where('id', $id)->update(['name'  => $request->name]);
        }

        #更新上级ID
        if ($request->superior) {
            $user = User::where('id', $id)->update(['bind_id' => $request->superior]);
        }


        if ($request->password) {
            $user = User::where('id', $id)->update(['password' => Hash::make($request->password)]);
        }

        return $this->success('更新用户成功')  ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        #保护超级管理员的帐号
        if ($id == 1) {
            return $this->failed('failed');
        }
        $User = User::findOrFail($id);

        $User = $User->delete();
        
        return $User ? $this->success('删除用户成功') : $this->notFond('删除用户失败,不存在对应用户') ;
    }
    /**
     * 按时间,管理ID,分页条数 function
     *
     * @param Request $request
     * @return void
     */
    public function operation(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $time = $request->time ?? false;
        $user_id = $request->user_id ?? false;
        $keyword = $request->keyword ?? false;


        $Operation = Operation::orderBy('id', 'DESC');

        if ($time) {
            $time = explode(' - ', $time);
            $Operation = $Operation->whereBetween('created_at', [$time[0],$time[1]]);
        }
        if ($user_id) {
            $Operation = $Operation->where('manager_id', $user_id);
        }

        if ($keyword) {
            $Operation = key_word_search($Operation, ['ip','path','input','method'], $keyword);
        }
        
        
        return   new UserCollection($Operation->with('user')->paginate($paginate));
    }

    
    /**
     * 按时间,管理ID,分页条数 function
     *
     * @param Request $request
     * @return void
     */
    public function activeLogin(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $time = $request->time ?? false;
        $user_id = $request->user_id ?? false;
        $keyword = $request->keyword ?? false;


        $ActiveLogin = ActiveLogin::orderBy('id', 'DESC');

        if ($time) {
            $time = explode(' - ', $time);
            $ActiveLogin = $ActiveLogin->whereBetween('created_at', [$time[0],$time[1]]);
        }
        if ($user_id) {
            $ActiveLogin = $ActiveLogin->where('user_id', $user_id);
        }

        

        return   new UserCollection($ActiveLogin->paginate($paginate));
    }



    /**
     * 权限层级菜单 function
     *
     * @return void
     */
    public function permissionMenu()
    {
        $permission = Permission::all()->toArray();

        // $permission = $permission->flatten(1)->values()->all();
        // $permission = json_decode(json_encode($permission) ,true);
        $tree = [];
        Tool::bMenu($permission, $tree);
        $treeLevel = [];

        foreach ($tree as $k => $t) {
            $treeLevel[] = $t;
        }
        return $this->success($treeLevel);
    }

    /**
     * 1.当前用户的角色 
     * 2.角色的权限列表 function
     *
     * @return void
     */
    public function permissionList()
    {
        $permission = Permission::all()->toArray();
        $user = $this->userInfoBase();
        #ID为1不判断
        if($user->id == 1){
            return $this->success($permission);
        }
        $data =[];
        #权限检测,以及返回前端的列表
        foreach($permission as $k =>  $p){
            if (mb_strstr($p['name'], 'skip') === false){

                #检测是否存在多权限的判断 ,进行分割处理 curd 与其它操作

                if ($user->can($p['name'])) {
                    $data[] =$p;
                }


            } else {
                $data[] =$p;
            }                
        }
        // $tree = [];
        // $treeLevel = [];
        // Tool::bMenuStatis($data, $tree);
        // foreach ($tree as $k => $t) {
        //     $treeLevel[] = $t;
        // }
        return $this->success($data);
    }

    /**
     * 用户锁定功能,默认ID为1不能操作 function
     *
     * @param Request $request
     * @return void
     */
    public function userLock(Request $request)
    {
        $id = $request->id ?? false;
        $operation = $request->operation ?? false;

        if ($id == 1) {
            return $this->failed('failed');
        }
        switch ($operation) {
            case 'lock':
                User::where('id', $id)->update(['status' => 1]);
                return $this->success('success');
                break;
            case 'unLock':
                User::where('id', $id)->update(['status' => 0]);
                return $this->success('success');
                break;
            default:
                return $this->failed('failed');
                break;
        }
    }
}
