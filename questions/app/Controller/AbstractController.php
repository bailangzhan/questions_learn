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
namespace App\Controller;

use App\Components\Response;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use OpenApi\Annotations as OA;
use Psr\Container\ContainerInterface;

/**
 * @OA\Info(title="问答系统API", version="1.0")
 * @OA\Server(url="http://127.0.0.1:9501", description="接口地址-本地")
 * @OA\Server(url="http://dev.XXX.com", description="接口地址-测试服")
 * @OA\Server(url="http://prod.XXX.com", description="接口地址-正式服")
 */
abstract class AbstractController
{
    #[Inject]
    protected ContainerInterface $container;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected Response $response;
}
