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
use App\Constants\KeyConstants;
use App\Event\UserSignuped;
use App\Exception\BusinessException;
use App\Model\User;
use App\Model\UserDynamic;
use App\Service\Instance\JwtInstance;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class UserService extends Service
{
    #[Inject]
    protected \App\Components\CacheManager $cacheManager;

    #[Inject]
    private EventDispatcherInterface $eventDispatcher;

    public function signup(array $params)
    {
        // 入库
        Db::beginTransaction();
        try {
            $model = new User();
            $model->email = $params['email'];
            $model->password = password_hash($params['password'], PASSWORD_DEFAULT);
            // 图片路径取决于各自保存在 cdn 的路径
            $model->pic = 'images/avatar/' . rand(1, 382) . '.jpg';
            $model->nickname = 'api_' . rand(1, 99) . date('Hi');
            $model->save();

            // 同步
            $dynamicModel = new UserDynamic();
            $dynamicModel->uid = $model->id;
            $dynamicModel->save();

            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            $this->logger->error($ex->getMessage());
            throw new BusinessException(ErrorCode::SERVER_ERROR);
        }

        // 获取token
        $token = JwtInstance::instance()->encode($model);
        $userInfo = JwtInstance::instance()->getUser();

        // 事件触发
        $this->eventDispatcher->dispatch(new UserSignuped($userInfo));

        return [$userInfo, $token];
    }

    public function login($email, $password)
    {
        // 校验用户是否存在
        $user = User::query()->where(['email' => $email])->first();
        if (empty($user)) {
            throw new BusinessException(ErrorCode::USER_NOT_EXISTS);
        }

        // 校验密码是否正确
        if (! password_verify($password, $user['password'])) {
            throw new BusinessException(ErrorCode::PASSWORD_ERROR);
        }

        // jwt 编码获取 token 和用户信息
        $token = JwtInstance::instance()->encode($user);
        $userInfo = JwtInstance::instance()->getUser();

        return [$userInfo, $token];
    }

    /**
     * 不通过注解也可以实现缓存.
     * @param mixed $id
     * @return array
     */
    public function getUserInfoFromCache($id)
    {
        $userModel = new User();

        $userInfo = $this->cacheManager->call(function () use ($userModel, $id) {
            return $userModel->getUserInfo($id);
        }, sprintf(KeyConstants::USER_INFO, $id), env('APP_ENV') == 'dev' ? 10 : 3600, 'other');

        if (! $userInfo) {
            throw new BusinessException(ErrorCode::USER_NOT_EXISTS);
        }

        return $userInfo;
    }

    /**
     * 批量操作.
     *
     * @param mixed $ids
     * @return array
     */
    public function getMultiUserInfosFromCache($ids)
    {
        $userModel = new User();

        return $this->cacheManager->multiCall(function ($targetIds) use ($userModel) {
            return $userModel->getUserInfos($targetIds);
        }, $ids, 'user', env('APP_ENV') == 'dev' ? 60 : 600, 'id', 'other');
    }
}
