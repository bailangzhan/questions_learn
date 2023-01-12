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
namespace App\Exception\Handler;

use App\Components\Response;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Validation\ValidationException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

class AppExceptionHandler extends ExceptionHandler
{
    protected $logger;

    /**
     * @var Response
     */
    protected $response;

    public function __construct(ContainerInterface $container, Response $response)
    {
        $this->logger = $container->get(LoggerFactory::class)->get('exception');
        $this->response = $response;
    }

    public function handle(\Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        $formatter = ApplicationContext::getContainer()->get(FormatterInterface::class);
        // 业务异常类
        if ($throwable instanceof BusinessException) {
            return $this->response->fail($throwable->getCode(), $throwable->getMessage());
        }

        // 针对表单的异常处理
        if ($throwable instanceof ValidationException) {
            $message = $throwable->validator->errors()->first();
            return $this->response->fail(ErrorCode::FORM_ERROR, $message);
        }

        // HttpException
        if ($throwable instanceof HttpException) {
            return $this->response->fail($throwable->getStatusCode(), $throwable->getMessage());
        }

        $this->logger->error($formatter->format($throwable));

        return $this->response->fail(500, env('APP_ENV') == 'dev' ? $throwable->getMessage() : 'Server Error');
    }

    public function isValid(\Throwable $throwable): bool
    {
        return true;
    }
}
