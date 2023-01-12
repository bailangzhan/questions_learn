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
    'http' => [
        \Hyperf\Validation\Middleware\ValidationMiddleware::class, // 验证器
        \App\Middleware\GlobalMiddleware::class, // 全局中间件
    ],
];
