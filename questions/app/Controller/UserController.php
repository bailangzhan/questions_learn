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
namespace App\Controller;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Request\LoginRequest;
use App\Request\SignUpRequest;
use App\Service\Instance\JwtInstance;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use OpenApi\Annotations as OA;

class UserController extends AbstractController
{
    #[Inject]
    protected UserService $userService;

    /**
     * @OA\Post(
     *     path="/user/signup",
     *     tags={"用户相关"},
     *     summary="注册",
     *     description="邮箱注册",
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"code", "email", "password", "password_confirmation"},
     *         @OA\Property(property="code", type="string", description="邮箱验证码"),
     *         @OA\Property(property="email", type="string", format="email", description="邮箱地址", example="a@a.com"),
     *         @OA\Property(property="password", type="string", description="密码"),
     *         @OA\Property(property="password_confirmation", type="string", description="确认密码"),
     *     )),
     *     @OA\Response(
     *         response=200,
     *         description="SUCCESS/成功",
     *         @OA\MediaType(
     *             mediaType="application/json; charset=utf-8",
     *             @OA\Schema(
     *                 @OA\Property(property="code", type="integer", format="int32", description="标识"),
     *                 @OA\Property(property="data", type="object", description="返回数据",
     *                     @OA\Property(property="user", type="object", description="用户信息",
     *                         @OA\Property(property="id", type="integer", description="用户ID"),
     *                         @OA\Property(property="email", type="string", description="用户邮箱"),
     *                         @OA\Property(property="pic", type="string", description="头像地址"),
     *                         @OA\Property(property="nickname", type="string", description="用户昵称"),
     *                         @OA\Property(property="create_time", type="integer", description="注册时间"),
     *                     ),
     *                     @OA\Property(property="token", type="string", description="token标识"),
     *                 )
     *             ),
     *             example={"code": 0, "data": {"user": {"id": 1, "email": "422744746@qq.com", "password": "xxx", "pic": "images\/avatar\/39.jpg", "nickname": "api_951403", "create_time": 1669097032}, "token": "xxx"}}
     *         ),
     *     )
     * )
     */
    /**
     * 注册.
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function signup(SignUpRequest $signUpRequest)
    {
        // 获取已验证的数据
        $validated = $signUpRequest->validated();

        [$user, $token] = $this->userService->signup($validated);

        return $this->response->success([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user/login",
     *     tags={"用户相关"},
     *     summary="登录",
     *     description="邮箱登录",
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"email", "password"},
     *         @OA\Property(property="email", type="string", format="email", description="邮箱地址", example="a@a.com"),
     *         @OA\Property(property="password", type="string", description="密码"),
     *     )),
     *
     *     @OA\Response(
     *         response=200,
     *         description="SUCCESS/成功",
     *         @OA\MediaType(
     *             mediaType="application/json; charset=utf-8",
     *             @OA\Schema(
     *                 @OA\Property(property="code", type="integer", format="int32", description="标识"),
     *                 @OA\Property(property="data", type="object", description="返回数据",
     *                     @OA\Property(property="user", type="object", description="用户信息",
     *                         @OA\Property(property="id", type="integer", description="用户ID"),
     *                         @OA\Property(property="email", type="string", description="用户邮箱"),
     *                         @OA\Property(property="pic", type="string", description="头像地址"),
     *                         @OA\Property(property="nickname", type="string", description="用户昵称"),
     *                         @OA\Property(property="create_time", type="integer", description="注册时间"),
     *                     ),
     *                     @OA\Property(property="token", type="string", description="token标识"),
     *                 )
     *             ),
     *             example={"code": 0, "data": {"user": {"id": 1, "email": "422744746@qq.com", "password": "xxx", "pic": "images\/avatar\/39.jpg", "nickname": "api_951403", "create_time": 1669097032}, "token": "xxx"}}
     *         ),
     *     )
     * )
     */
    /**
     * 登录.
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login(LoginRequest $loginRequest)
    {
        $validated = $loginRequest->validated();

        [$user, $token] = $this->userService->login($validated['email'], $validated['password']);

        return $this->response->success([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function test()
    {
        $token = $this->request->input('token');

        $jwtInstance = JwtInstance::instance();
        try {
            $jwtInstance->decode($token);
        } catch (\Throwable $e) {
        }

        $uid = $jwtInstance->getId();
        if (empty($uid)) {
            throw new BusinessException(ErrorCode::FORBIDDEN);
        }

        return $this->response->success([
            'uid' => $uid,
        ]);
    }
}
