<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Role;
use App\Http\Requests\Admin\RoleRequest;
use App\Http\Resources\Admin\Role as RoleCollection;
use App\Http\Resources\Admin\RoleArray;

use App\Permission;

class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = $request->paginate ?? 20;


        return new RoleCollection(Role::paginate($paginate));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }
    /**
     * 角色附加权限操作 [数组传入参数] function
     *
     * @return void
     */
    public function role2Permission(Request $request)
    {
        $permission = $request->permission ?? [];
        $role_id = $request->role_id ?? '';

        if (empty($role_id) && empty($permission)) {
            return $this->message('角色与权限不能为空');
        }
        $role = Role::findOrFail($role_id);
        $permission = explode(',', rtrim($permission, ',')) ;

        $role->savePermissions($permission);

        return $this->success('更新角色权限成功');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $name = $request->name;
        $display_name = $request->display_name;
        $description = $request->description;

        $role = new Role();
        $role->name = $name;
        $role->display_name = $display_name;
        $role->description = $description;
        $role->save();

        return  $this->success($role);
    }

    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $perms = $role = Role::find($id)->perms()->get() ;

        return ( new RoleArray(Role::find($id)) )
                ->additional(['meta' => [
                    'permission' => $perms,
                    ]]);
        ;
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
     * 编辑的操作更新,没view所以不写edit
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $id =  Role::where('id', $id)->update($request->all());

        return $id ? $this->success('更新角色成功') : $this->notFond('不存在的ID更新') ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role = $role->delete();
        
        return $role ? $this->success('删除角色成功') : $this->notFond('删除角色失败,不存在对应角色') ;
    }
}
