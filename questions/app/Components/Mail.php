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
use Hyperf\Contract\ContainerInterface;
use Hyperf\Logger\LoggerFactory;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    public $to;

    protected $log;

    protected $mail;

    public function __construct(ContainerInterface $container)
    {
        $this->log = $container->get(LoggerFactory::class)->get('mail');
        $this->init();
    }

    public function to($to)
    {
        $this->to = $to;
        return $this;
    }

    public function send($subject, $body)
    {
        try {
            $mail = $this->mail;

            // Recipients
            $mail->addAddress($this->to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
        } catch (\Exception $e) {
            $this->log->error('邮件发送失败：' . $mail->ErrorInfo);
            throw new BusinessException(ErrorCode::MAIL_SEND_FAILED);
        }
    }

    protected function init()
    {
        $mail = $this->mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = env('MAIL_SMTP_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = env('MAIL_SMTP_USERNAME');
        $mail->Password = env('MAIL_SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = env('MAIL_SMTP_PORT');
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    }
}
