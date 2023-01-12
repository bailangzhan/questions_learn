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
    // 默认语言
    'locale' => 'zh_CN',
    // 回退语言，当默认语言的语言文本没有提供时，就会使用回退语言的对应语言文本
    'fallback_locale' => 'en',
    // 语言文件存放的文件夹
    'path' => BASE_PATH . '/storage/languages',
];
