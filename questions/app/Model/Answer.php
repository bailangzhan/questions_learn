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
namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $uid
 * @property int $question_id
 * @property int $pid
 * @property string $content
 * @property int $supports
 * @property int $create_time
 */
class Answer extends Model
{
    public const CREATED_AT = 'create_time';

    public const UPDATED_AT = null;

    protected ?string $dateFormat = 'U';

    protected ?string $table = 'answer';

    protected array $fillable = ['uid', 'question_id', 'pid', 'content', 'supports'];

    protected array $casts = ['id' => 'integer', 'uid' => 'integer', 'question_id' => 'integer', 'create_time' => 'integer',
        'pid' => 'integer', 'supports' => 'integer',
    ];
}
