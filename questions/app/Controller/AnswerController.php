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

use App\Constants\Constants;
use App\Request\AnswerSaveRequest;
use App\Request\AnswerSupportRequest;
use App\Service\AnswerService;
use App\Service\Instance\JwtInstance;
use Hyperf\Di\Annotation\Inject;

class AnswerController extends AbstractController
{
    #[Inject]
    protected AnswerService $answerService;

    public function save(AnswerSaveRequest $answerSaveRequest)
    {
        $uid = JwtInstance::instance()->getId();
        $validated = $answerSaveRequest->validated();
        $this->answerService->save($uid, $validated['question_id'], $validated['pid'], $validated['content']);

        return $this->response->success();
    }

    public function list(int $questionId)
    {
        // 这里需要再确认下是否需要捕获 ->decode 代码段，参考 AuthMiddleware 中间件
        $token = $this->request->getHeaderLine(Constants::AUTHORIZATION);
        ! empty($token) && JwtInstance::instance()->decode($token);
        $uid = JwtInstance::instance()->getId();

        return $this->response->success($this->answerService->list($uid, $questionId));
    }

    public function support(AnswerSupportRequest $answerSupportRequest)
    {
        $uid = JwtInstance::instance()->getId();
        $validated = $answerSupportRequest->validated();
        $this->answerService->support($uid, $validated['answer_id']);

        return $this->response->success();
    }
}
