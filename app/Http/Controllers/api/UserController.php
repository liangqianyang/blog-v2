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
    public function login(Request $request): string
    {
        $data = ['code' => 0, 'message' => '登录成功', 'token' => ''];
        try {
            // 检索验证过的输入数据...
//            $validated = $request->validated();
//            var_dump($validated);die;
            $username = $request->post('username');
            $password = $request->post('password');
            $user = User::where('user_name', $username)->first();
            if (!$user || !Hash::check($password, $user->password)) {
                throw ValidationException::withMessages([
                    'user_name' => ['密码不正确。'],
                ]);
            }
            $data['token'] = $user->createToken($username, ['*'], now()->addDay())->plainTextToken;
        } catch (\Exception $e) {
            $data['code'] = 1001;
            $data['message'] = $e->getMessage();
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
