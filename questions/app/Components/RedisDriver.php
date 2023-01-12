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

use Hyperf\Redis\RedisProxy;
use Psr\Container\ContainerInterface;

class RedisDriver extends \Hyperf\Cache\Driver\RedisDriver
{
    public function __construct(ContainerInterface $container, array $config)
    {
        parent::__construct($container, $config);

        $this->redis = make(RedisProxy::class, ['pool' => 'cache']);
    }
}
