<?php

namespace App\Component;

class Helpers
{
    /**
     * 构建树形结构
     * @param  array  $items  数据列表
     * @param  int  $parentId  父级ID
     * @param  string  $key  唯一标识键名
     */
    public static function buildTree(array $items, int $parentId = 0, string $key = 'id'): array
    {
        $branch = [];
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = self::buildTree($items, $item[$key], $key);
                if ($children) {
                    $item['children'] = $children;
                }
                $branch[] = $item;
            }
        }

        return $branch;
    }
}
