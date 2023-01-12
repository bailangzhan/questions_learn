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
namespace App\Listener;

use App\Service\MailService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\Event\ValidatorFactoryResolved;
use Psr\Container\ContainerInterface;

#[Listener]
class ValidatorFactoryResolvedListener implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            ValidatorFactoryResolved::class,
        ];
    }

    public function process(object $event): void
    {
        /** @var ValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $event->validatorFactory;
        // 注册验证器
        $validatorFactory->extend('verifyCode', function ($attribute, $value, $parameters, $validator) {
            // 获取所有属性
            $attributes = $validator->attributes();
            $email = $attributes['email'] ?? '';
            $code = $value;

            return $this->container->get(MailService::class)->verifyCode($email, $code);
        });
    }
}
