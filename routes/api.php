<?php


use App\Http\Controllers\api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/user/login', [UserController::class, 'login']);

});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // 退出登录
    Route::post('/user/login-out', [UserController::class, 'loginOut']);
});
