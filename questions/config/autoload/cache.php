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
return [
    'default' => [
        'driver' => Hyperf\Cache\Driver\RedisDriver::class,
        'packer' => Hyperf\Utils\Packer\PhpSerializerPacker::class,
        'prefix' => 'c:',
    ],
    // 还可以配置其他的缓存
    'other' => [
        // 更换driver达到更换redis连接的问题
        'driver' => \App\Components\RedisDriver::class,
        'packer' => Hyperf\Utils\Packer\JsonPacker::class,
        'prefix' => 'o:',
    ],
];
