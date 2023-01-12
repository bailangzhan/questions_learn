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

use Hyperf\Cache\CacheManager;

class Cache extends \Hyperf\Cache\Cache
{
    public function __construct(CacheManager $manager, $driver = 'default')
    {
        $this->driver = $manager->getDriver($driver);
    }
}
