<?php

namespace App\Models;

use App\Component\Exception\WarningException;
use App\Component\LogHelper;
use App\Http\Requests\AssignPermissionsRequest;
use App\Http\Requests\ChangeRoleStateRequest;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
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
        $query = $query->orderBy('id', 'desc');
        return $query->get();
    }

    /**
     * 创建角色
     * @param CreateRoleRequest $request
     * @return array
     */
    public function store(CreateRoleRequest $request): array
    {
        $success = true;
        $message = '保存成功';
        try {
            $params = $request->validated();
            $role = Role::query()->where('name', $params['name'])->first();
            if (!empty($role)) {
                throw new WarningException('角色已存在');
            }
            $role = new Role();
            $role->name = $params['name'];
            $role->save();
        } catch (\Exception $e) {
            $success = false;
            $message = '保存失败，' . $e->getMessage();
            LogHelper::error('角色保存失败,error:' . $e->getMessage(), $e);
        }
        return [$success, $message];
    }

    /**
     * 更新角色
     * @param UpdateRoleRequest $request
     * @return array
     */
    public function updateRole(UpdateRoleRequest $request): array
    {
        $success = true;
        $message = '更新成功';
        try {
            $params = $request->validated();
            $role = Role::query()->where('name', $params['name'])->first();
            if (!empty($role)) {
                throw new WarningException('角色已存在');
            }
            $role = Role::query()->find($params['id']);
            if (empty($role)) {
                throw new WarningException('角色不存在');
            }
            $role->name = $params['name'];
            $role->save();
        } catch (\Exception $e) {
            $success = false;
            $message = '更新失败，' . $e->getMessage();
            LogHelper::error('角色更新失败,error:' . $e->getMessage(), $e);
        }
        return [$success, $message];
    }


    /**
     * 更改角色状态
     * @param ChangeRoleStateRequest $request
     * @return array
     */
    public function changeState(ChangeRoleStateRequest $request): array
    {
        $success = true;
        $message = '操作成功';
        try {
            $params = $request->validated();
            $role = Role::query()->find($params['id']);
            if (empty($role)) {
                throw new WarningException('角色不存在');
            }
            $role->state = $role->state == 1 ? -1 : 1;
            $role->save();
        } catch (\Exception $e) {
            $success = false;
            $message = '操作失败，' . $e->getMessage();
            LogHelper::error('角色状态修改失败,error:' . $e->getMessage(), $e);
        }
        return [$success, $message];
    }

    /**
     * 分配权限
     * @param AssignPermissionsRequest $request
     * @return array
     */
    public function assignPermissions(AssignPermissionsRequest $request): array
    {
        $success = true;
        $message = '保存成功';
        try {
            $params = $request->validated();
            $roleId = $params['role_id'];
            $permissionIds = $params['permission_ids'];
            $role = Role::query()->find($roleId);
            if (empty($role)) {
                throw new WarningException('角色不存在');
            }
            $permissions = Permission::query()->whereIn('id', $permissionIds)->get()->toArray();
            if (empty($permissions)) {
                throw new WarningException('权限不存在');
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
            $success = false;
            $message = '保存失败，' . $e->getMessage();
            LogHelper::error('角色权限保存失败,error:' . $e->getMessage(), $e);
        }
        return [$success, $message];
    }

    /**
     * 获取角色权限
     * @param int $roleId
     * @return array
     */
    public function getRolePermissions(int $roleId): array
    {
        $data = [];
        $permissionIds = [];
        try {
            $res = MenuPermission::query()->leftJoin('menus', 'menus.id', '=', 'menu_permissions.menu_id')
                ->leftJoin('permissions', 'permissions.id', '=', 'menu_permissions.permission_id')
                ->where('menus.state', '>=', 0)
                ->where('permissions.state', '>=', 0)
                ->select('menus.id as menu_id', 'menus.name as menu_name', 'permissions.id as permission_id', 'permissions.name as permission_name')
                ->get()->toArray();
            $rolePermissions = RolePermission::query()->where('role_id', $roleId)->get()->toArray();
            $permissionIds = array_column($rolePermissions, 'permission_id');
            $menuPermissions = [];
            foreach ($res as $item) {
                $menuPermissions[$item['menu_id']]['id'] = 'menu_' . $item['menu_id'];
                $menuPermissions[$item['menu_id']]['label'] = $item['menu_name'];
                $menuPermissions[$item['menu_id']]['children'][] = [
                    'id' => $item['permission_id'],
                    'label' => $item['permission_name']
                ];
            }
            $data = array_values($menuPermissions);
        } catch (\Exception $e) {
            Log::error('获取角色权限失败,error:' . $e->getMessage());
        }
        return [$data, $permissionIds];
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
                        $query->where('state', $value);
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
