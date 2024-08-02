<?php

namespace App\Component;

class Helpers
{
    /**
     * 构建树形结构
     * @param array $items
     * @param int $parentId
     * @return array
     */
    public static function buildTree(array $items, int $parentId = 0): array
    {
        $branch = [];
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = self::buildTree($items, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $branch[] = $item;
            }
        }
        return $branch;
    }
}
