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
namespace App\Amqp\Consumer;

use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Hyperf\Utils\ApplicationContext;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'mail', routingKey: 'mail', queue: 'mail', name: 'MailConsumer', nums: 1)]
class MailConsumer extends ConsumerMessage
{
    public function consumeMessage($data, AMQPMessage $message): string
    {
        $mail = ApplicationContext::getContainer()->get(\App\Components\Mail::class);
        $mail->to($data['to'])->send($data['subject'], $data['body']);

        return Result::ACK;
    }
}
