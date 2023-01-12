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
use Hyperf\Constants\Annotation\Constants as AnnotationConstants;

#[AnnotationConstants]
class KeyConstants extends AbstractConstants
{
    public const USER_INFO = 'user:%d';

    public const QUESTION_HOT_RANK = 'question_hot';

    public const QUESTION_INFO = 'question:%d';
}
