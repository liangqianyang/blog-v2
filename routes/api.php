<?php

use App\Http\Controllers\api\MenuController;
use App\Http\Controllers\api\PermissionController;
use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::prefix('v1')->group(function () {
    Route::post('/user/login', [UserController::class, 'login']);

});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // 退出登录
    Route::post('/user/login-out', [UserController::class, 'loginOut']);
    // 菜单列表
    Route::get('/menu/list', [MenuController::class, 'list']);
    // 导航菜单
    Route::get('/menu/navbar', [MenuController::class, 'navbarMenu']);
    // 菜单选择列表
    Route::get('/menu/select-list', [MenuController::class, 'menuSelectList']);
    // 添加菜单
    Route::post('/menu/store', [MenuController::class, 'store']);
    // 更新菜单
    Route::put('/menu/update', [MenuController::class, 'update']);
    // 修改菜单状态
    Route::put('/menu/change-state', [MenuController::class, 'changeMenuState']);
    // 权限列表
    Route::get('/permission/list', [PermissionController::class, 'list']);
    // 添加权限
    Route::post('/permission/store', [PermissionController::class, 'create']);
    // 修改权限状态
    Route::put('/permission/change-state', [PermissionController::class, 'changeState']);
    // 角色列表
    Route::get('/role/list', [RoleController::class, 'list']);
    // 角色权限
    Route::get('/role/permissions', [RoleController::class, 'getRolePermissions']);
    // 创建角色
    Route::post('/role/store', [RoleController::class, 'store']);
    // 更新角色
    Route::put('/role/update', [RoleController::class, 'update']);
    // 修改角色状态
    Route::put('/role/change-state', [RoleController::class, 'changeState']);
    // 分配权限
    Route::post('/role/assign-permissions', [RoleController::class, 'assignPermissions']);
    // 分配角色
    Route::post('/user/assign-roles', [UserController::class, 'assignRoles']);
});
