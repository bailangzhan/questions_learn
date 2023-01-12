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
namespace App\Components;

class CacheManager extends \Hyperf\Cache\CacheManager
{
    public function multiCall($callback, array $ids, string $cacheKey, int $ttl = 3600, string $primaryKey = 'id', $config = 'default')
    {
        $driver = $this->getDriver($config);

        $keys = [];
        foreach ($ids as $id) {
            $keys[] = $cacheKey . ':' . $id;
        }

        $data = $driver->getMultiple($keys);
        $result = [];
        if ($data) {
            foreach ($data as $k => $v) {
                if (isset($v[$primaryKey])) {
                    $result[$v[$primaryKey]] = $v;
                }
            }
        }

        $fetchIds = array_column((array) $data, $primaryKey);
        $targetIds = array_diff($ids, $fetchIds);
        if ($targetIds) {
            $callResult = \call($callback, [$targetIds]);
            if ($callResult) {
                $setResults = [];
                foreach ($callResult as $k => &$v) {
                    $result[$v[$primaryKey]] = $v;
                    $setResults[$cacheKey . ':' . $v[$primaryKey]] = $v;
                }
                unset($v);
                $driver->setMultiple($setResults, $ttl);
            }
        }

        return $result;
    }
}
