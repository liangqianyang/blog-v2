<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Requests\AssignRolesRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'nickname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * 分配角色
     * @param AssignRolesRequest $request
     * @return array
     */
    public function assignRoles(AssignRolesRequest $request): array
    {
        $result = true;
        $message = '保存成功';
        try {
            $params = $request->validated();
            $userId = $params['user_id'];
            $roleIds = explode(',', $params['role_id']);
            $user = User::query()->find($userId);
            if (empty($user)) {
                throw new \ErrorException('用户不存在');
            }
            $roles = Role::query()->whereIn('id', $roleIds)->get()->toArray();
            if (empty($roles)) {
                throw new \ErrorException('角色不存在');
            }
            $newRoleIds = array_column($roles, 'id');
            DB::transaction(function () use ($userId, $newRoleIds) {
                RoleUser::query()->where('user_id', $userId)->delete();
                foreach ($newRoleIds as $roleId) {
                    $roleUser = new RoleUser();
                    $roleUser->user_id = $userId;
                    $roleUser->role_id = $roleId;
                    $roleUser->save();
                }
            });
        } catch (\Exception $e) {
            $result = false;
            $message = '保存失败，' . $e->getMessage();
            Log::error('用户角色保存失败,error:' . $e->getMessage());
        }
        return [$result, $message];
    }

    /**
     * 获取用户的角色，权限
     * @param $userId
     * @return array
     */
    public function getUserRolePermissions($userId): array
    {
        $role = '';
        $roleId = '';
        $permissions = [];
        $roleUser = RoleUser::query()->where('user_id', $userId)->get()->toArray();
        if (!empty($roleUser)) {
            $roleId = array_column($roleUser, 'role_id');
            $roleIds = array_unique($roleId);
            $roles = Role::query()->whereIn('id', $roleIds)->where('state', 1)->get()->toArray();
            $role = array_column($roles, 'name');
            $rolePermissions = RolePermission::query()->whereIn('role_id', $roleIds)->get()->toArray();
            $permissions = array_unique(array_column($rolePermissions, 'permission_id'));
        }
        $role = implode(',', $role);
        $roleId = implode(',', $roleId);
        return [$role, $roleId, $permissions];
    }
}
