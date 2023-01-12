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

use Hyperf\Context\Context;
use Hyperf\Contract\TranslatorInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GlobalMiddleware implements MiddlewareInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(ContainerInterface $container, TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($lang = $request->getHeaderLine('lang')) {
            $this->translator->setLocale($lang);
        }

        // trace
        $span = Context::get('tracer.root');
        $response = Context::get(ResponseInterface::class);
        $response = $response->withHeader('Trace-Id', $span->getContext()->getContext()->getTraceId());
        Context::set(ResponseInterface::class, $response);

        return $handler->handle($request);
    }
}
