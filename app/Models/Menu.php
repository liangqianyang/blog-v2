<?php

namespace App\Models;

use App\Component\Helpers;
use App\Http\Requests\CreateMenuRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;


class Menu extends Model
{
    use HasFactory;

    /**
     * 获取菜单列表的结果
     * @param array $params
     * @return array
     */
    public function result(array $params): array
    {
        $query = Menu::query();
        $query = $this->filterQueryConditions($query, $params);
        $data = $query->get()->toArray();
        $menuIds = array_column($data, 'id');
        $parentIds = array_column($data, 'parent_id');
        $parentIds = array_unique(array_filter($parentIds));
        $menuIds = array_unique(array_merge($menuIds, $parentIds));
        $data = Menu::query()->whereIn('id', $menuIds)->orderBy('sort', 'asc')->get()->toArray();
        return Helpers::buildTree($data);
    }

    /**
     * 获取菜单选择列表
     * @return array
     */
    public function getMenuSelectList(): array
    {
        $data = [];
        try {
            $list = Menu::query()->select(['id as value', 'parent_id', 'name as label'])
                ->where(['state' => 1])->orderBy('sort', 'asc')->get()->toArray();
            $menuIds = array_column($list, 'value');
            $parentIds = array_column($list, 'parent_id');
            $parentIds = array_unique(array_filter($parentIds));
            $menuIds = array_unique(array_merge($menuIds, $parentIds));
            $data = Menu::query()->select(['id as value', 'parent_id', 'name as label'])->whereIn('id', $menuIds)->get()->toArray();
        } catch (\Exception $e) {
            Log::error('获取菜单选择列表失败,error:' . $e->getMessage());
        }
        return Helpers::buildTree($data, 0, 'value');
    }

    /**
     * 获取导航菜单
     * @param $userId
     * @return array
     */
    public function getNavbarMenus($userId): array
    {
        $data = [];
        try {
            $userRoles = RoleUser::query()->where('user_id', $userId)->get()->toArray();
            if (empty($userRoles)) {
                return $data;
            }
            $roleIds = array_column($userRoles, 'role_id');
            $rolePermissions = RolePermission::query()->whereIn('role_id', $roleIds)->get()->toArray();
            if (empty($rolePermissions)) {
                return $data;
            }
            $permissionIds = array_column($rolePermissions, 'permission_id');
            $menuPermissions = MenuPermission::query()->whereIn('permission_id', $permissionIds)->get()->toArray();
            $menuIds = array_unique(array_column($menuPermissions, 'menu_id'));
            $menus = Menu::query()->whereIn('id', $menuIds)->where('state', 1)->orderBy('sort')->get()->toArray();
            $parentIds = array_column($menus, 'parent_id');
            $parentIds = array_unique(array_filter($parentIds));
            $menuIds = array_unique(array_merge($menuIds, $parentIds));
            $menus = Menu::query()->whereIn('id', $menuIds)->orderBy('sort')->get()->toArray();
            $data = Helpers::buildTree($menus);
            foreach ($data as &$item) {
                if ($item['parent_id'] == 0 && !empty($item['children'])) {
                    $item['redirect'] = $item['path'] . '/' . $item['children'][0]['path'];
                    foreach ($item['children'] as &$child) {
                        $child['meta'] = ['title' => $child['name'], 'icon' => $child['icon']];
                        $component = !empty($child['component']) ? $child['component'] : '';
                        $componentArr = explode('/', $component);
                        if ($componentArr) {
                            unset($componentArr[0]);
                        }
                        // 将$componentArr中的元素转换为大写
                        $componentArr = array_map('ucfirst', $componentArr);
                        $child['component'] = 'views/' . implode('/', $componentArr);
                    }
                }
                $item['meta'] = ['title' => $item['name'], 'icon' => $item['icon']];

            }
        } catch (\Exception $e) {
            Log::error('获取导航菜单失败,error:' . $e->getMessage());
        }
        return $data;
    }

    /**
     * 过滤查询条件
     * @param Builder $query
     * @param array $conditions
     * @return Builder
     */
    private function filterQueryConditions(Builder $query, array $conditions): Builder
    {
        if (!empty($conditions)) {
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
        }
        return $query;
    }

    public function store(CreateMenuRequest $request)
    {
        $result = true;
        $message = '保存成功';
        try {
            $params = $request->validated();
            $menu = Menu::query()->where('name', $params['name'])->first();
            if (!empty($menu)) {
                throw new \ErrorException('菜单已存在');
            }
            $menu = new Menu();
            $menu->parent_id = $params['parent_id'];
            $menu->name = $params['name'];
            $menu->component = $params['component'];
            $menu->path = $params['path'];
            $menu->icon = !empty($params['icon']) ? $params['icon'] : '';
            $menu->sort = !empty($params['sort']) ? $params['sort'] : '';
            $menu->state = 1;
            $menu->save();
        } catch (\Exception $e) {
            $result = false;
            $message = '保存失败，' . $e->getMessage();
            Log::error('菜单保存失败,error:' . $e->getMessage());
        }
        return [$result, $message];
    }
}


