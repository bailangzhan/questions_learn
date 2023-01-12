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

use App\Components\FileAdapter;
use App\Constants\ErrorCode;
use App\Constants\KeyConstants;
use App\Exception\BusinessException;
use App\Model\Question;
use App\Model\QuestionDynamic;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\RedisProxy;

class QuestionService extends Service
{
    #[Inject]
    protected \App\Components\CacheManager $cacheManager;

    /**
     * 入库.
     * @param mixed $uid
     * @param mixed $title
     * @param mixed $content
     * @return array
     */
    public function save($uid, $title, $content)
    {
        // 标题+内容 hash，避免内容重复
        $contentHash = hash('sha256', $title . '_' . $content);
        $question = Question::query()->where(['content_hash' => $contentHash])->first();
        if (! empty($question)) {
            throw new BusinessException(ErrorCode::QUESTION_EXISTS);
        }

        $date = date('Y-m-d');
        $path = "contents/{$date}/{$contentHash}.txt";

        // 上传至七牛
        $adapter = make(FileAdapter::class);
        $adapter->write($path, $content);

        Db::beginTransaction();
        try {
            $model = new Question();
            $model->uid = $uid;
            $model->title = $title;
            $model->content_path = $path;
            $model->content_hash = $contentHash;
            $model->save();

            // 同步
            $dynamicModel = new QuestionDynamic();
            $dynamicModel->question_id = $model['id'];
            $dynamicModel->save();

            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new BusinessException(ErrorCode::SERVER_ERROR);
        }

        return ['id' => $model['id']];
    }

    public function search($keyword, $pageSize)
    {
        if (empty($keyword)) {
            throw new BusinessException(ErrorCode::QUESTION_KEYWORD_EMPTY);
        }
        $searchRes = Question::search($keyword)->paginate($pageSize);
        $searchRes = $searchRes->toArray();

        if ($result['data'] = $searchRes['data']) {
            $result['total'] = $searchRes['total'];
            $result['data'] = $this->listFormatter($result['data']);
        }
        return $result;
    }

    /**
     * 详情.
     * @param mixed $id
     * @return array
     */
    public function info($id)
    {
        $info = $this->getInfoFromCache($id);

        $info['user'] = make(UserService::class)->getUserInfoFromCache($info['uid']);

        // 更新 views
        $this->updateDynamic($id, 'views');

        // 热门榜单
        $redis = make(RedisProxy::class, ['pool' => 'persistence']);
        $redis->zIncrBy(KeyConstants::QUESTION_HOT_RANK, 1, $id);

        return $info;
    }

    public function updateDynamic($id, $column, $num = 1)
    {
        return QuestionDynamic::query()->where(['question_id' => $id])->increment($column, $num);
    }

    public function getInfoFromCache($id, $throw = true)
    {
        $key = sprintf(KeyConstants::QUESTION_INFO, $id);
        $info = $this->cacheManager->call(function () use ($id) {
            return $this->getInfo($id);
        }, $key, env('APP_ENV') == 'dev' ? 300 : 600, 'other');

        if (! $info && $throw) {
            throw new BusinessException(ErrorCode::QUESTION_NOT_EXISTS);
        }
        return $info;
    }

    public function list(int $pageSize)
    {
        $questions = Question::query()
            ->select('id', 'uid', 'title', 'create_time')
            ->orderByDesc('id')
            ->paginate($pageSize)
            ->toArray();
        $questions['data'] = $this->listFormatter($questions['data']);

        return $questions;
    }

    protected function getInfo($id)
    {
        $info = Question::query()->where('id', $id)->select('id', 'uid', 'title', 'content_path', 'create_time')->first();
        if (empty($info)) {
            // 避免缓存穿透
            return null;
        }
        $info = $info->toArray();

        // content
        $adapter = make(FileAdapter::class);
        $contentUrl = $adapter->privateDownloadUrl($info['content_path']);
        $info['content'] = $adapter->read($contentUrl);

        $info['views'] = 0;
        $info['answers'] = 0;
        $info['comments'] = 0;
        $info['replys'] = 0;
        $info['supports'] = 0;

        $dynamicRes = QuestionDynamic::query()->where('question_id', $id)->select('question_id', 'views', 'comments', 'replys', 'supports')->first();
        if ($dynamicRes) {
            $info['views'] = $dynamicRes['views'];
            $info['comments'] = $dynamicRes['comments'];
            $info['replys'] = $dynamicRes['replys'];
            $info['answers'] = $dynamicRes['comments'] + $dynamicRes['replys']; // 回答数=评论数+回复数
            $info['supports'] = $dynamicRes['supports'];
        }

        if (isset($info['content_path'])) {
            unset($info['content_path']);
        }

        return $info;
    }

    /**
     * 处理列表数据.
     * @param $data
     * @return mixed
     */
    protected function listFormatter($data)
    {
        if ($data) {
            $uids = [];
            $ids = [];
            foreach ($data as $k => $v) {
                $data[$k]['views'] = 0;
                $data[$k]['answers'] = 0;
                $data[$k]['comments'] = 0;
                $data[$k]['replys'] = 0;
                $data[$k]['supports'] = 0;
                $ids[] = $v['id'];
                $uids[] = $v['uid'];
            }
            $uids = array_unique($uids);

            // 动态数据
            $dynamicRes = QuestionDynamic::query()
                ->whereIn('question_id', $ids)
                ->select('question_id', 'views', 'comments', 'replys', 'supports')
                ->get()
                ->toArray();
            $dynamicRes = array_column($dynamicRes, null, 'question_id');

            // 用户信息
            $userInfos = make(UserService::class)->getMultiUserInfosFromCache($uids);

            // 处理
            foreach ($data as $k => $v) {
                if (! empty($dynamicRes[$v['id']])) {
                    $data[$k]['views'] = $dynamicRes[$v['id']]['views'];
                    $data[$k]['comments'] = $dynamicRes[$v['id']]['comments'];
                    $data[$k]['replys'] = $dynamicRes[$v['id']]['replys'];
                    $data[$k]['supports'] = $dynamicRes[$v['id']]['supports'];
                    $data[$k]['answers'] = $dynamicRes[$v['id']]['comments'] + $dynamicRes[$v['id']]['replys'];
                }
                if (! empty($userInfos)) {
                    $data[$k]['user'] = $userInfos[$v['uid']] ?? new \stdClass();
                }

                unset($data[$k]['content_path'], $data[$k]['content_hash']);
            }
        }

        return $data;
    }

    /**
     * 热榜
     * @return array
     */
    public function hotRank()
    {
        $redis = make(RedisProxy::class, ['pool' => 'persistence']);
        $hotQuestions = $redis->zrevrange(KeyConstants::QUESTION_HOT_RANK, 0, 9, true);
        $hotQuestionIds = array_keys($hotQuestions);

        $result = [];
        $sort = [];
        if ($hotQuestionIds) {
            $questions = Question::query()
                ->select('id', 'uid', 'title', 'create_time')
                ->whereIn('id', $hotQuestionIds)
                ->get()
                ->toArray();
            if ($questions) {
                foreach ($questions as $k => $v) {
                    $sort[$k] = $v['hot_value'] = $hotQuestions[$v['id']];
                    $result[] = $v;
                }
            }
        }

        // 按热度排序
        $sort && array_multisort($sort, SORT_DESC, $result);

        return $result;
    }
}
