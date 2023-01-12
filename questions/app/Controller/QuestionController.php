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

use App\Request\QuestionSaveRequest;
use App\Service\Instance\JwtInstance;
use App\Service\QuestionService;
use Hyperf\Di\Annotation\Inject;

class QuestionController extends AbstractController
{
    #[Inject]
    protected QuestionService $questionService;

    public function save(QuestionSaveRequest $questionSaveRequest)
    {
        $uid = JwtInstance::instance()->getId();
        $validated = $questionSaveRequest->validated();

        return $this->response->success($this->questionService->save($uid, $validated['title'], $validated['content']));
    }

    public function search()
    {
        $keyword = $this->request->input('keyword', '');
        $pageSize = (int) $this->request->input('pageSize', 10);
        return $this->response->success($this->questionService->search($keyword, $pageSize));
    }

    public function info(int $id)
    {
        return $this->response->success($this->questionService->info($id));
    }

    public function list()
    {
        $pageSize = (int) $this->request->input('pageSize', 10);
        return $this->response->success($this->questionService->list($pageSize));
    }

    public function hotRank()
    {
        return $this->response->success($this->questionService->hotRank());
    }
}
