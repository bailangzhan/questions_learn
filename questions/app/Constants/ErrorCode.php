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
namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

#[Constants]
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("Server Error！")
     */
    public const SERVER_ERROR = 500;

    public const FORBIDDEN = 403;

    /**
     * @Message("params.id_invalid")
     */
    public const PARAMS_ID_INVALID = 100001;

    /**
     * @Message("mail.sent_failed")
     */
    public const MAIL_SEND_FAILED = 100002;

    /**
     * @Message("mail.sent_frequently")
     */
    public const CODE_SENT_FREQUENTLY = 100003;

    /**
     * @Message("mail.code_error")
     */
    public const CODE_ERROR = 100004;

    /**
     * @Message("mail.code_expired")
     */
    public const CODE_EXPIRED = 100005;

    public const FORM_ERROR = 100006;

    /**
     * @Message("file.upload_failed")
     */
    public const FILE_UPLOAD_FAILED = 100007;

    /**
     * @Message("file.read_failed")
     */
    public const FILE_READ_FAILED = 100008;

    /**
     * @Message("user.exists")
     */
    public const USER_EXISTS = 200001;

    /**
     * @Message("user.token_expired")
     */
    public const TOKEN_EXPIRED = 200002;

    /**
     * @Message("user.not_exists")
     */
    public const USER_NOT_EXISTS = 200003;

    /**
     * @Message("user.password_error")
     */
    public const PASSWORD_ERROR = 200004;

    /**
     * @Message("user.token_invalid")
     */
    public const TOKEN_INVALID = 200005;

    /**
     * @Message("question.exists")
     */
    public const QUESTION_EXISTS = 300001;

    /**
     * @Message("question.keyword_empty")
     */
    public const QUESTION_KEYWORD_EMPTY = 300002;

    /**
     * @Message("question.not_exists")
     */
    public const QUESTION_NOT_EXISTS = 300003;
}
