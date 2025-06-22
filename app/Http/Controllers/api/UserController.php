<?php

namespace App\Http\Controllers\api;

use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public User $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * 登录
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $result = ['code' => 0, 'message' => '登录成功', 'data' => []];
        try {
            $username = $request->post('username');
            $password = $request->post('password');
            $user = User::query()->where('username', $username)->first();
            if (! $user || ! Hash::check($password, $user->password)) {
                throw ValidationException::withMessages([
                    'username' => ['密码不正确。'],
                ]);
            }
            $data['token'] = $user->createToken($username, ['*'], now()->addDay())->plainTextToken;
            $userData = [
                'username' => $user->username,
            ];
            $data['user'] = $userData;
            $result['data'] = $data;
        } catch (\Exception $e) {
            $result['code'] = -1;
            $result['message'] = $e->getMessage();
        }

        return response()->json($result);
    }

    /**
     * 退出登录
     */
    public function loginOut(Request $request): JsonResponse
    {
        $data = ['code' => 0, 'message' => '退出成功'];
        try {
            $request->user()->currentAccessToken()->delete();
        } catch (\Exception $e) {
            $data['code'] = -1;
            $data['message'] = $e->getMessage();
        }

        return response()->json($data);
    }

    /**
     * 获取用户角色
     */
    public function roles($id): JsonResponse
    {
        $user = User::query()->findOrFail($id);

        return response()->json($user->roles);
    }

    /**
     * 分配角色
     */
    public function assignRole(Request $request, $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        $data = $request->validate([
            'roles'   => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);
        $user->syncRoles($data['roles']);

        return response()->json($user->roles);
    }

    /**
     * 移除角色
     */
    public function removeRole($id, $role_id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        $role = Role::query()->findOrFail($role_id);
        $user->removeRole($role);

        return response()->json($user->roles);
    }

    /**
     * 获取用户权限
     */
    public function permissions($id): JsonResponse
    {
        $user = User::query()->findOrFail($id);

        return response()->json($user->getAllPermissions());
    }

    /**
     * 分配权限
     */
    public function givePermission(Request $request, $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        $data = $request->validate([
            'permissions'   => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);
        $user->givePermissionTo($data['permissions']);

        return response()->json($user->getAllPermissions());
    }

    /**
     * 移除权限
     */
    public function revokePermission($id, $permission_id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        $permission = Permission::query()->findOrFail($permission_id);
        $user->revokePermissionTo($permission);

        return response()->json($user->getAllPermissions());
    }
}
