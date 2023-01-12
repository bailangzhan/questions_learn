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
 * @property string $password
 * @property string $pic
 * @property string $nickname
 * @property int $create_time
 */
class User extends Model
{
    public const CREATED_AT = 'create_time';

    public const UPDATED_AT = null;

    protected ?string $table = 'users';

    protected ?string $dateFormat = 'U';

    protected array $fillable = ['email', 'password', 'pic', 'nickname'];

    protected array $casts = ['id' => 'integer', 'create_time' => 'integer'];

    /**
     * @param mixed $id
     * @return array
     */
    public function getUserInfo($id)
    {
        $userInfo = User::query()->where('id', $id)->select('id', 'email', 'pic', 'create_time', 'nickname')->first();

        return empty($userInfo) ? null : $userInfo->toArray();
    }

    /**
     * @return mixed[]
     */
    public function getUserInfos(array $ids)
    {
        return User::query()->whereIn('id', $ids)->select('id', 'email', 'pic', 'create_time', 'nickname')->get()->toArray();
    }
}
