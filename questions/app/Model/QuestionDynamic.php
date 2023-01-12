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
 * @property int $question_id
 * @property int $views
 * @property int $comments
 * @property int $replys
 * @property int $supports
 */
class QuestionDynamic extends Model
{
    public bool $timestamps = false;

    public string $primaryKey = 'question_id';

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'question_dynamic';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['views', 'question_id', 'comments', 'replys', 'supports'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['question_id' => 'integer', 'views' => 'integer', 'comments' => 'integer', 'replys' => 'integer', 'supports' => 'integer'];
}
