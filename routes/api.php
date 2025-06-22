<?php

use App\Http\Controllers\api\PermissionController;
use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/user/login', [UserController::class, 'login']);

});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // 退出登录
    Route::post('/user/login-out', [UserController::class, 'loginOut']);

    Route::prefix('roles')->group(function () {
        // 角色列表
        Route::get('/', [RoleController::class, 'index']);
        // 创建角色
        Route::post('/', [RoleController::class, 'store']);
        // 获取角色详情
        Route::get('{id}', [RoleController::class, 'show']);
        // 更新角色
        Route::put('{id}', [RoleController::class, 'update']);
        // 删除角色
        Route::delete('{id}', [RoleController::class, 'destroy']);
    });

    Route::prefix('permissions')->group(function () {
        // 权限列表
        Route::get('/', [PermissionController::class, 'index']);
        // 创建权限
        Route::post('/', [PermissionController::class, 'store']);
        // 获取权限详情
        Route::get('{id}', [PermissionController::class, 'show']);
        // 更新权限
        Route::put('{id}', [PermissionController::class, 'update']);
        // 删除权限
        Route::delete('{id}', [PermissionController::class, 'destroy']);
    });

    Route::prefix('users')->group(function () {
        // 用户角色
        Route::get('{id}/roles', [UserController::class, 'roles']);
        // 分配角色
        Route::post('{id}/roles', [UserController::class, 'assignRole']);
        // 移除角色
        Route::delete('{id}/roles/{role_id}', [UserController::class, 'removeRole']);
        Route::get('{id}/permissions', [UserController::class, 'permissions']);
        // 分配权限
        Route::post('{id}/permissions', [UserController::class, 'givePermission']);
        // 移除权限
        Route::delete('{id}/permissions/{permission_id}', [UserController::class, 'revokePermission']);
    });
});
