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
namespace App\Aspect;

use App\Components\FileAdapter;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Tracer\SpanStarter;
use Hyperf\Tracer\SpanTagManager;
use Hyperf\Tracer\SwitchManager;
use OpenTracing\Tracer;

#[Aspect]
class FileAdapterAspect extends AbstractAspect
{
    use SpanStarter;

    public array $classes = [
        FileAdapter::class,
    ];

    public function __construct(private Tracer $tracer, private SwitchManager $switchManager, private SpanTagManager $spanTagManager)
    {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $key = $proceedingJoinPoint->className . '::' . $proceedingJoinPoint->methodName;

        $arguments = $proceedingJoinPoint->arguments['keys'];
        $span = $this->startSpan($key);
        $span->setTag($this->spanTagManager->get('params', 'params'), json_encode($arguments));

        try {
            $result = $proceedingJoinPoint->process();
        } catch (\Throwable $e) {
            $span->setTag('error', true);
            $span->log(['message', $e->getMessage(), 'code' => $e->getCode(), 'stacktrace' => $e->getTraceAsString()]);
            throw $e;
        } finally {
            $span->finish();
        }
        return $result;
    }
}
