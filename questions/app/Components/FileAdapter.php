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
namespace App\Components;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Filesystem\FilesystemFactory;
use League\Flysystem\Config;
use League\Flysystem\UnableToWriteFile;
use Psr\Container\ContainerInterface;

class FileAdapter
{
    protected ContainerInterface $container;

    protected \League\Flysystem\FilesystemAdapter $adapter;

    public function __construct(ContainerInterface $container, $adapterName = 'qiniu')
    {
        $this->container = $container;
        $this->adapter = $this->getAdapter($adapterName);
    }

    public function write($path, $content)
    {
        try {
            return $this->adapter->write($path, $content, new Config());
        } catch (UnableToWriteFile $e) {
            throw new BusinessException(ErrorCode::FILE_UPLOAD_FAILED);
        } catch (\Throwable $e) {
            throw new BusinessException(ErrorCode::SERVER_ERROR);
        }
    }

    public function privateDownloadUrl($path)
    {
        try {
            return $this->adapter->privateDownloadUrl($path);
        } catch (\Throwable $e) {
            throw new BusinessException(ErrorCode::SERVER_ERROR);
        }
    }

    public function read($url)
    {
        try {
            return $this->adapter->read($url);
        } catch (\Throwable $e) {
            throw new BusinessException(ErrorCode::FILE_READ_FAILED);
        }
    }

    protected function getAdapter($adapterName)
    {
        $options = $this->container->get(ConfigInterface::class)->get('file');
        $filesystemFactory = $this->container->get(FilesystemFactory::class);
        return $filesystemFactory->getAdapter($options, $adapterName);
    }
}
