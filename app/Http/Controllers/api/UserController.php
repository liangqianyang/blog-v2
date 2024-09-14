<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * 登录
     * @param UserLoginRequest $request
     * @return string
     */
    public function login(UserLoginRequest $request): string
    {
        $data = ['code' => 0, 'message' => '登录成功', 'token' => ''];
        try {
            $username = $request->post('username');
            $password = $request->post('password');
            $user = User::where('username', $username)->first();
            if (!$user || !Hash::check($password, $user->password)) {
                throw ValidationException::withMessages([
                    'username' => ['密码不正确。'],
                ]);
            }
            $data['token'] = $user->createToken($username, ['*'], now()->addDay())->plainTextToken;
        } catch (\Exception $e) {
            $data['code'] = -1;
            $data['message'] = $e->getMessage();
        }
        return json_encode($data);
    }

    /**
     * 退出登录
     * @param Request $request
     * @return string
     */
    public function loginOut(Request $request): string
    {
        $data = ['code' => 0, 'message' => '退出成功'];
        try {
            $request->user()->currentAccessToken()->delete();
        } catch (\Exception $e) {
            $data['code'] = -1;
            $data['message'] = $e->getMessage();
        }
        return json_encode($data);
    }
}
