<?php

namespace App\Models;

use App\Http\Requests\AssignPermissionsRequest;
use App\Http\Requests\CreateRoleRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Role extends Model
{
    use HasFactory;

    /**
     * 列表查询结果
     * @param array $params
     * @return Collection
     */
    public function result(array $params): Collection
    {
        $query = Role::query();
        $query = $this->filterQueryConditions($query, $params);
        $query = $query->orderBy('sort', 'asc');
        return $query->get();
    }

    /**
     * 创建角色
     * @param CreateRoleRequest $request
     * @return array
     */
    public function store(CreateRoleRequest $request): array
    {
        $result = true;
        $message = '保存成功';
        try {
            $params = $request->validated();
            $role = Role::query()->where('name', $params['name'])->first();
            if (!empty($role)) {
                throw new \ErrorException('角色已存在');
            }
            $role = new Role();
            $role->name = $params['name'];
            $role->save();
        } catch (\Exception $e) {
            $result = false;
            $message = '保存失败，' . $e->getMessage();
            Log::error('角色保存失败,error:' . $e->getMessage());
        }
        return [$result, $message];
    }

    /**
     * 分配权限
     * @param AssignPermissionsRequest $request
     * @return array
     */
    public function assignPermissions(AssignPermissionsRequest $request): array
    {
        $result = true;
        $message = '保存成功';
        try {
            $params = $request->validated();
            $roleId = $params['role_id'];
            $permissionIds = explode(',', $params['permission_id']);
            $role = Role::query()->find($roleId);
            if (empty($role)) {
                throw new \ErrorException('角色不存在');
            }
            $permissions = Permission::query()->whereIn('id', $permissionIds)->get()->toArray();
            if (empty($permissions)) {
                throw new \ErrorException('权限不存在');
            }
            $newPermissionIds = array_column($permissions, 'id');
            DB::transaction(function () use ($roleId, $newPermissionIds) {
                RolePermission::query()->where('role_id', $roleId)->delete();
                foreach ($newPermissionIds as $permissionId) {
                    $rolePermission = new RolePermission();
                    $rolePermission->role_id = $roleId;
                    $rolePermission->permission_id = $permissionId;
                    $rolePermission->save();
                }
            });
        } catch (\Exception $e) {
            $result = false;
            $message = '保存失败，' . $e->getMessage();
            Log::error('角色权限保存失败,error:' . $e->getMessage());
        }
        return [$result, $message];
    }

    /**
     * 过滤查询条件
     * @param Builder $query
     * @param array $conditions
     * @return Builder
     */
    private function filterQueryConditions(Builder $query, array $conditions): Builder
    {
        if (empty($conditions)) {
            return $query;
        }
        $fields = $this->getAttributes();
        foreach ($conditions as $key => $value) {
            if ($value) {
                switch ($key) {
                    case 'name':
                        $query->where('name', 'like', "$value%");
                        break;
                    case 'state':
                        $query->where('name', $value);
                        break;
                    default:
                        if (isset($fields[$key])) {
                            $query->where([$this->getTable() . '.' . $key => $value]);
                        }
                        break;
                }
            }
        }
        return $query;
    }

    /**
     * 关联权限
     * @return HasMany
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }
}
