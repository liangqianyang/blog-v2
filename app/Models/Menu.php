<?php

namespace App\Models;

use App\Component\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Menu extends Model
{
    use HasFactory;

    public function result(array $params): array
    {
        $query = Menu::query();
        $query = $this->filterQueryConditions($query, $params);
        $query = $query->orderBy('sort', 'asc');
        $data = $query->get()->toArray();
        if (!empty($params)) {
            $parentIds = array_column($data, 'parent_id');
            $parentIds = array_unique(array_filter($parentIds));
            $parentMenus = Menu::query()->whereIn('id', $parentIds)->get()->toArray();
            $data = array_merge($data, $parentMenus);
        }
        return Helpers::buildTree($data);
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
}


