<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignRolesRequest;
use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public User $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * 登录
     * @param UserLoginRequest $request
     * @return string
     */
    public function login(UserLoginRequest $request): string
    {
        $result = ['code' => 0, 'message' => '登录成功', 'data' => []];
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
            $userModel = new User();
            list($role, $roleId, $permissions) = $userModel->getUserRolePermissions($user->id);
            $userData = [
                'username' => $user->username,
                'role' => $role,
                'roleId' => $roleId,
                'permissions' => $permissions
            ];
            $data['user'] = $userData;
            $result['data'] = $data;
        } catch (\Exception $e) {
            $result['code'] = -1;
            $result['message'] = $e->getMessage();
        }
        return json_encode($result);
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

    /**
     * 分配角色
     * @param AssignRolesRequest $request
     * @return string
     */
    public function assignRoles(AssignRolesRequest $request): string
    {
        $result = ['code' => 0, 'message' => '保存成功'];
        list($res, $message) = $this->model->assignRoles($request);
        if (!$res) {
            $result['code'] = -1;
            $result['message'] = $message;
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
