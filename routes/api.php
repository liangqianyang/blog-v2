<?php

use App\Http\Controllers\api\MenuController;
use App\Http\Controllers\api\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::prefix('v1')->group(function () {
    Route::post('/user/login', [UserController::class,'login']);
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/menu/list', [MenuController::class,'list']);
});
