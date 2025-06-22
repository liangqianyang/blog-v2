<?php

/**
 * Created by PhpStorm.
 * User: bb
 * Date: 2019/7/17
 * Time: 9:51
 */

use Illuminate\Support\Facades\Route;

/**
 * 将当前请求的路由名称转换为CSS类名称
 */
function routeClass(): ?string
{
    return str_replace('.', '-', Route::currentRouteName());
}

/**
 * 根据数组中的某个键值大小进行排序，仅支持二维数组
 *
 * @param  array  $array  排序数组
 * @param  string  $key  键值
 * @param  bool  $asc  默认正序
 * @return array 排序后数组
 */
function arraySortByKey(array $array, string $key, bool $asc = true): array
{
    $result = [];
    // 整理出准备排序的数组
    foreach ($array as $k => &$v) {
        $values[$k] = $v[$key] ?? '';
    }
    unset($v);
    // 对需要排序键值进行排序
    $asc ? asort($values) : arsort($values);
    // 重新排列原有数组
    foreach ($values as $k => $v) {
        $result[$k] = $array[$k];
    }

    return $result;
}

/**
 * 获取图片的Base64编码(不支持url)
 */
function imageToBase64(string $imageFile): string
{
    $imgBase64 = '';
    if (file_exists($imageFile)) {
        $appImgFile = $imageFile; // 图片路径
        $imgInfo = getimagesize($appImgFile); // 取得图片的大小，类型等
        $fp = fopen($appImgFile, 'r'); // 图片是否可读权限
        if ($fp) {
            $filesize = filesize($appImgFile);
            $content = fread($fp, $filesize);
            $file_content = chunk_split(base64_encode($content)); // base64编码
            $imgType = match ($imgInfo[2]) {
                1 => 'gif',
                3 => 'png',
                default => 'jpg',
            };
            $imgBase64 = 'data:image/'.$imgType.';base64,'.$file_content; // 合成图片的base64编码
        }
        fclose($fp);
    }

    return $imgBase64; // 返回图片的base64
}

/**
 * 把多维数组转为一维数组
 */
function reduce(array $array): array
{
    $return = [];
    array_walk_recursive($array, function ($x, $index) use (&$return) {
        $return[] = $x;
    });

    return $return;
}

/**
 * 生成随机字符串
 */
function getRandomStr(int $len, ?string $chars = null): string
{
    if (is_null($chars)) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    }
    mt_srand(10000000 * (float) microtime());
    for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}
