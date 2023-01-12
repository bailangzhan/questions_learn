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

/**
 * @property int $id
 * @property string $email
 * @property string $code
 * @property int $create_time
 */
class EmailCode extends Model
{
    public const CREATED_AT = 'create_time';

    public const UPDATED_AT = null;

    protected ?string $table = 'email_code';

    protected ?string $dateFormat = 'U';

    protected array $fillable = ['email', 'code'];

    protected array $casts = ['id' => 'integer', 'create_time' => 'integer'];
}
