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

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Model\EmailCode;
use App\Model\User;
use Hyperf\Utils\ApplicationContext;

class MailService extends Service
{
    /**
     * 通过邮件获取验证码
     * @param mixed $email
     * @return mixed
     */
    public function getCode($email)
    {
        $time = time();

        // 限制频率 5分钟3次
        $sentNum = EmailCode::query()
            ->where('email', $email)
            ->where('create_time', '>', $time - 300)
            ->count();
        if ($sentNum >= 3) {
            throw new BusinessException(ErrorCode::CODE_SENT_FREQUENTLY);
        }

        // 判断用户是否存在
        $user = User::query()->where(['email' => $email])->first();
        if (! empty($user)) {
            throw new BusinessException(ErrorCode::USER_EXISTS);
        }

        $code = rand(1000, 9999);
        $model = new EmailCode();
        $model->email = $email;
        $model->code = $code;
        $model->save();

        try {
            co(function () use ($email, $code) {
                $mail = ApplicationContext::getContainer()->get(\App\Components\Mail::class);
                $mail->to($email)->send('帐号激活', '您的验证码是： <b style="color: #f00;">' . $code . '</b>');
            });
        } catch (\Exception $e) {
            throw new BusinessException(ErrorCode::MAIL_SEND_FAILED);
        }

        return true;
    }

    /**
     * 校验验证码
     * @param mixed $email
     * @param mixed $code
     * @return bool
     */
    public function verifyCode($email, $code)
    {
        if (empty($email) || empty($code)) {
            throw new BusinessException(ErrorCode::CODE_ERROR);
        }

        $result = EmailCode::query()
            ->where(['email' => $email, 'code' => $code])
            ->orderBy('id', 'desc')
            ->first();
        if (empty($result)) {
            throw new BusinessException(ErrorCode::CODE_ERROR);
        }
        if ($result['create_time'] < time() - 300) {
            throw new BusinessException(ErrorCode::CODE_EXPIRED);
        }

        return true;
    }
}
