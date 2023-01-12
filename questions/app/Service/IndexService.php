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

use App\Amqp\Producer\MailProducer;
use App\Model\User;
use Hyperf\Amqp\Producer;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Psr\SimpleCache\CacheInterface;

class IndexService
{
    public function info()
    {
        $uid = 1;

        $container = ApplicationContext::getContainer();
//        $cache = $container->get(\Psr\SimpleCache\CacheInterface::class);
        $cache = make(CacheInterface::class, ['driver' => 'other']);
        $userKey = sprintf('userInfo:%d', $uid);
        $userInfo = $cache->get($userKey);
        if (is_null($userInfo)) {
            $userModel = new User();
            $userInfo = $userModel->getUserInfo($uid);
            $cache->set($userKey, $userInfo, 60);
        }

        return $userInfo;
//        $startTime = microtime(true);
//
//        $mailInfo = [
//            'to' => '422744746@qq.com',
//            'subject' => '邮件测试标题111',
//            'body' => '<b style="color: #f00;">邮件测试内容222</b>',
//        ];
//        $message = new MailProducer($mailInfo);
//        $producer = ApplicationContext::getContainer()->get(Producer::class);
//        $producer->produce($message);
//
        // //        co(function () {
        // //            $mail = ApplicationContext::getContainer()->get(\App\Components\Mail::class);
        // //            $mail->to('422744746@qq.com')->send('邮件测试标题', '<b style="color: #f00;">邮件测试内容</b>');
        // //
        // //            $log = ApplicationContext::getContainer()->get(LoggerFactory::class)->get('co');
        // //            $log->info("邮件已发送.");
        // //        });
//
//        $runTime = '耗时: ' . (microtime(true) - $startTime) . ' s';
//
//        return ['time' => date('Y-m-d H:i:s'), 'runtime' => $runTime];
    }
}
