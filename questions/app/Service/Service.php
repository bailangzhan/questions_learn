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
namespace App\Service;

use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;

abstract class Service
{
    protected ContainerInterface $container;

    /**
     * @var LoggerFactory
     */
    protected \Psr\Log\LoggerInterface|LoggerFactory $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(LoggerFactory::class)->get('service');
    }
}
