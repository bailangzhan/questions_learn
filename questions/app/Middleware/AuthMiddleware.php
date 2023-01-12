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
namespace App\Middleware;

use App\Constants\Constants;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Service\Instance\JwtInstance;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine(Constants::AUTHORIZATION);

        if (! empty($token)) {
            $jwtInstance = JwtInstance::instance();
            try {
                $jwtInstance->decode($token);
            } catch (\Throwable $e) {
            }

            $uid = $jwtInstance->getId();
            if (empty($uid)) {
                throw new BusinessException(ErrorCode::FORBIDDEN);
            }
        } else {
            throw new BusinessException(ErrorCode::FORBIDDEN);
        }

        return $handler->handle($request);
    }
}
