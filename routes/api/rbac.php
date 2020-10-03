<?php

$router->resource('user', 'UserController');
$router->resource('role', 'RoleController');
$router->resource('permission', 'PermissionController');

#附加的权限处理与角色处理.
$router->get('permissions/all', 'PermissionController@all');
$router->post('role2Permission', 'RoleController@role2Permission');

$router->post('user2Role', 'UserController@user2Role')
                ->name('user2Role.skip');

$router->get('users/operation', 'UserController@operation')
            ->name('operation.index');
$router->get('users/activeLogin', 'UserController@activeLogin')
            ->name('activeLogin.index');
$router->get('users/userInfo', 'UserController@userInfo')
            ->name('user.info.skip');
$router->get('users/permissionMenu', 'UserController@permissionMenu')
            ->name('user.permissionMenu.skip');
$router->post('users/userLock', 'UserController@userLock')
            ->name('user.userLock');

#permission 权限 菜单 对应当前登录用户的权限列表 为1直接返回所有不检测
$router->get('users/permissionList','UserController@permissionList')
        ->name('users.permissionList.skip');



