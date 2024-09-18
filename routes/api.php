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
    Route::post('/user/login-out', [UserController::class, 'loginOut']);
    Route::get('/menu/list', [MenuController::class, 'list']);
    Route::get('/menu/navbar', [MenuController::class, 'navbarMenu']);
    Route::post('/permission/store', [PermissionController::class, 'create']);
    Route::post('/role/store', [RoleController::class, 'create']);
    Route::post('/role/assign-permissions', [RoleController::class, 'assignPermissions']);
    Route::post('/user/assign-roles', [UserController::class, 'assignRoles']);
});
