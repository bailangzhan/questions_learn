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
use App\Exception\BusinessException;
use App\Model\Answer;
use App\Model\Question;
use App\Model\QuestionDynamic;
use App\Model\UserDynamic;
use Hyperf\DbConnection\Db;
use Hyperf\Redis\RedisProxy;
use Hyperf\Utils\ApplicationContext;

class AnswerService extends Service
{
    public function save($uid, $questionId, $pid, $content)
    {
        $question = Question::query()->where(['id' => $questionId])->first();
        if (empty($question)) {
            throw new BusinessException(ErrorCode::QUESTION_NOT_EXISTS);
        }

        Db::beginTransaction();
        try {
            // 入库
            $model = new Answer();
            $model->uid = $uid;
            $model->question_id = $questionId;
            $model->pid = $pid;
            $model->content = $content;
            $model->save();

            // 同步更新
            UserDynamic::query()->where(['uid' => $uid])->increment('answers');
            if ($pid > 0) {
                QuestionDynamic::query()->where(['question_id' => $questionId])->increment('replys');
            } else {
                QuestionDynamic::query()->where(['question_id' => $questionId])->increment('comments');
            }

            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new BusinessException(ErrorCode::SERVER_ERROR);
        }

        // 热门榜单
        $redis = make(RedisProxy::class, ['pool' => 'persistence']);
        $redis->zIncrBy(KeyConstants::QUESTION_HOT_RANK, 5, $questionId);

        return true;
    }

    public function list($uid, $questionId)
    {
        $list = Answer::query()
            ->select('id', 'uid', 'question_id', 'pid', 'content', 'supports', 'create_time')
            ->where(['question_id' => $questionId])
            ->get()
            ->toArray();
        if ($list) {
            $answerIds = array_column($list, 'id');
            $uids = array_unique(array_column($list, 'uid'));

            // 用户信息
            $userInfos = ApplicationContext::getContainer()->get(UserService::class)->getMultiUserInfosFromCache($uids);

            // 判断当前用户是否已点赞
            $answerSupports = array_fill_keys($answerIds, 0);
            if (! empty($uid)) {
                $redis = make(RedisProxy::class, ['pool' => 'persistence']);
                $pipeline = $redis->pipeline();
                foreach ($answerIds as $answerId) {
                    $pipeline->getBit(sprintf(KeyConstants::ANSWER_SUPPORT, $answerId), $uid);
                }
                $res = $pipeline->exec();
                if ($res) {
                    foreach ($answerIds as $key => $answerId) {
                        $answerSupports[$answerId] = $res[$key];
                    }
                }
            }

            foreach ($list as $k => $v) {
                $list[$k]['is_support'] = $answerSupports[$v['id']];
                if (! empty($userInfos)) {
                    $list[$k]['user'] = $userInfos[$v['uid']] ?? new \stdClass();
                }
            }

            $list = generate_tree($list);
        }

        return $list;
    }

    public function support($uid, $answerId)
    {
        $info = Answer::query()->where(['id' => $answerId])->first();
        if (empty($info)) {
            throw new BusinessException(ErrorCode::QUESTION_ANSWER_NOT_EXISTS);
        }

        // 判断当前用户是否已经点过赞了
        // 这里需要考虑key是以用户为中心还是以点赞的评论为中心，因为列表要批量判断用户点赞了哪些评论，可以通过get获取然后再解析，这样会有一个新问题，如果数据量比较大，比如百万级或者千万级，offset也会导致内存溢出。
        // 如果以评论为中心，可以通过 pipeline 管道批量 getBit, 推荐使用这种方法，
        $supportKey = sprintf('answer:support:%d', $answerId);
        $redis = make(RedisProxy::class, ['pool' => 'persistence']);
        $has = $redis->getBit($supportKey, $uid);
        if ($has) {
            throw new BusinessException(ErrorCode::QUESTION_ANSWER_SUPPORTED);
        }
        // 赞 标识
        $redis->setBit($supportKey, $uid, true);

        QuestionDynamic::query()->where(['question_id' => $info['question_id']])->increment('supports');
        UserDynamic::query()->where(['uid' => $info['uid']])->increment('supports');
        $info->increment('supports');

        return true;
    }
}
