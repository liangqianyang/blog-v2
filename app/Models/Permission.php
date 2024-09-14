<?php

namespace App\Models;

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
     * @return Collection
     */
    public function result(array $params): Collection
    {
        $query = Permission::query();
        $query = $this->filterQueryConditions($query, $params);
        $query = $query->orderBy('sort', 'asc');
        return $query->get();
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
     * 关联菜单
     * @return HasOne
     */
    public function menu(): HasOne
    {
        return $this->hasOne(Menu::class);
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
