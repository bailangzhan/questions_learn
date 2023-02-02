<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
if (! function_exists('convert_size')) {
    /**
     * 将字节转化为 kb mb 等单位.
     * @param mixed $size
     * @return string
     */
    function convert_size($size)
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        return @round($size / pow(1024, $i = floor(log($size, 1024))), 2) . ' ' . $unit[$i];
    }
}

if (! function_exists('generate_tree')) {
    /**
     * 生成树.
     */
    function generate_tree($data, string $id = 'id', string $parentTag = 'pid', string $childTag = 'child'): array
    {
        $items = [];
        foreach ($data as $value) {
            $items[$value[$id]] = $value;
        }
        $tree = [];
        foreach ($items as $k => $v) {
            if (isset($items[$v[$parentTag]])) {
                $items[$v[$parentTag]][$childTag][] = &$items[$k];
            } else {
                $tree[] = &$items[$k];
            }
        }

        return $tree;
    }
}
