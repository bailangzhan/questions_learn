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
 * @property int $uid
 * @property int $answers
 * @property int $supports
 */
class UserDynamic extends Model
{
    public bool $timestamps = false;

    protected ?string $table = 'user_dynamic';

    protected array $fillable = ['uid', 'answers', 'supports'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['uid' => 'integer', 'answers' => 'integer', 'supports' => 'integer'];
}
