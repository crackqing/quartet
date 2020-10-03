<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Permission;

use App\Http\Resources\Admin\Permission as PermissionCollection;
use App\Http\Resources\Admin\PermissionResource;

use App\Http\Requests\Admin\RoleRequest;


class PermissionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new PermissionCollection(Permission::paginate(20));
    }

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        return new PermissionCollection(Permission::all());
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
    public function store(RoleRequest $request)
    {
        $name = $request->name; 
        $display_name = $request->display_name;
        $description = $request->description;
        $pid = $request->pid ?? 0;

        $Permission = new Permission();
        $Permission->name = $name;
        $Permission->display_name = $display_name;
        $Permission->description = $description;
        $Permission->pid = $pid;
        $Permission->save();

        return  $this->success($Permission);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $id =  Permission::where('id',$id)->update($request->except(['name']));

        return $id ? $this->success('更新权限成功') : $this->notFond('不存在的ID权限更新') ;
    }

    /**
     * 删除时，判断上级是否存在， 如果存在的情况下，则需要提示s
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        $permission = $permission->delete();
        
        return $permission ? $this->success('删除权限成功') : $this->notFond('删除权限失败,不存在对应ID') ;
    }

}
