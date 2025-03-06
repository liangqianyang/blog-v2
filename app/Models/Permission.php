<?php

namespace App\Models;

use App\Http\Requests\ChangePermissionStateRequest;
use App\Http\Requests\CreatePermissionRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Permission extends Model
{
    use HasFactory;

    /**
     * 列表查询结果
     * @param array $params
     * @return array
     */
    public function result(array $params): array
    {
        $page = $params['page'] ?? 1;
        $pageSize = $params['page_size'] ?? 10;
        $query = Permission::query()->leftJoin('menu_permissions', 'permissions.id', '=', 'menu_permissions.permission_id')
            ->leftJoin('menus', 'menu_permissions.menu_id', '=', 'menus.id')
            ->select(['permissions.*', 'menu_permissions.menu_id', 'menus.name as menu_name']);
        $count = $query->count();
        $query = $this->filterQueryConditions($query, $params);
        $query = $query->orderBy('id', 'asc');
        return [$count, $query->forPage($page, $pageSize)->get()];
    }

    /**
     * 创建权限
     * @param CreatePermissionRequest $request
     * @return array
     */
    public function store(CreatePermissionRequest $request): array
    {
        $result = false;
        $message = '保存失败';
        try {
            $id = $request->input('id');
            $menuId = $request->input('menu_id');
            $name = $request->input('name');
            $code = $request->input('code');
            $description = $request->input('description');
            $permission = Permission::query()->where('code', $code)->where('id', '<>', $id)->first();
            if (!empty($permission)) {
                throw new \ErrorException('权限标识已存在');
            }
            DB::transaction(function () use ($id, $name, $code, $description, $menuId) {
                if ($id) {
                    $permission = Permission::query()->where('id', $id)->first();
                } else {
                    $permission = new Permission();
                }
                $permission->name = $name;
                $permission->code = $code;
                $permission->description = $description;
                $permission->save();
                if (!$id) {
                    $menuPermission = new MenuPermission();
                    $menuPermission->menu_id = $menuId;
                    $menuPermission->permission_id = $permission->id;
                    $menuPermission->save();
                } else {
                    $menuPermission = MenuPermission::query()->where('permission_id', $id)->first();
                    if ($menuPermission) {
                        $menuPermission->menu_id = $menuId;
                        $menuPermission->save();
                    }
                }
            });
            $result = true;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error('权限保存失败,error:' . $e->getMessage());
        }
        return [$result, $message];
    }


    /**
     * 保存角色权限
     * @param CreatePermissionRequest $request
     * @return array
     */
    public function storeRolePermission(CreatePermissionRequest $request): array
    {
        $result = false;
        $message = '保存失败';
        try {
            $menuId = $request->input('menu_id');
            $name = $request->input('name');
            $code = $request->input('code');
            $description = $request->input('description');
            $menu = Menu::query()->find($menuId);
            if (empty($menu)) {
                throw new \ErrorException('菜单不存在');
            }
            $permission = Permission::query()->where('code', $code)->first();
            if (!empty($permission)) {
                throw new \ErrorException('权限已存在');
            }
            DB::transaction(function () use ($menuId, $name, $code, $description) {
                $permission = new Permission();
                $permission->name = $name;
                $permission->code = $code;
                $permission->description = $description;
                $permission->save();

                $menuPermission = new MenuPermission();
                $menuPermission->menu_id = $menuId;
                $menuPermission->permission_id = $permission->id;
                $menuPermission->save();
            });
            $result = true;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error('权限保存失败,error:' . $e->getMessage());
        }
        return [$result, $message];
    }

    /**
     * 更改权限状态
     * @param ChangePermissionStateRequest $request
     * @return array
     */
    public function changeState(ChangePermissionStateRequest $request): array
    {
        $success = true;
        $message = '操作成功';
        try {
            $params = $request->validated();
            $permission = Permission::query()->find($params['id']);
            if (empty($permission)) {
                throw new \ErrorException('权限不存在');
            }
            $permission->state = $permission->state == 1 ? -1 : 1;
            if ($permission->state == -1) {
                $rolePermission = RolePermission::query()->where('permission_id', $params['id'])->first();
                if (!empty($rolePermission)) {
                    throw new \ErrorException('权限已分配给角色，不可停用');
                }
            }
            $permission->save();
        } catch (\Exception $e) {
            $success = false;
            $message = '操作失败，' . $e->getMessage();
            Log::error('权限状态修改失败,error:' . $e->getMessage());
        }
        return [$success, $message];
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
}
