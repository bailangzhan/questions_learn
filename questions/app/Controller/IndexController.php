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

use App\Components\FileAdapter;
use App\Service\IndexService;
use App\Service\Instance\JwtInstance;
use App\Service\UserService;
use Hyperf\Config\Config;
use Hyperf\Context\Context;
use Hyperf\Contract\ConfigInterface;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Coroutine;

#[AutoController]
class IndexController extends AbstractController
{
    /**
     * @var IndexService
     */
    #[Inject]
    public $indexService;

    #[Inject]
    public UserService $userService;

    public static $a = [];

    public $b = [];

    public function bus()
    {
        $this->b[] = str_repeat("'hello world'", 1024);

//        static::$a[] = str_repeat("'hello world'", 1024);
        var_dump(convert_size(memory_get_usage(true)));

        return [
//            'count' => count(static::$a),
//            'a' => static::$a,
//            'use' => convert_size(memory_get_usage(true)),
        ];
    }

    public function info()
    {
        global $previous;
        $current = memory_get_usage();
        $stats = [
            'prev_mem' => $previous,
            'curr_mem' => $current,
            'diff_mem' => $current - $previous,
        ];
        $previous = $current;

        return [
            'stats' => $stats,
        ];

//        // 如果在协程环境下创建，则会自动使用协程版的 Handler，非协程环境下无改变
//        $builder = $this->container->get(ClientBuilderFactory::class)->create();
//
//        $client = $builder->setHosts([env('ELASTICSEARCH_HOST')])->build();
//
////        $info = $client->info();
////
////        $params = [
////            'index' => 'question',
////            'type' => '_doc',
////            'id' => '2'
////        ];
////        $qInfo = $client->get($params);
//
//        $params = [
//            'index' => 'question',
//            'type' => '_doc',
//        ];
//
////        $params['body']['query']['bool']['must']['match']['title'] = '第3个';
////        $params['body']['query']['bool']['filter']['term']['id'] = '2';
//        $params['body']['query']['bool']['filter']['range']['create_time']['gte'] = '1672901482';
//        $matchRes = $client->search($params);
//
//        return [
//            //            'info' => $info,
//            //            'qInfo' => $qInfo,
//            'matchInfo' => $matchRes,
//        ];
//
//
        // //        $adapter = make(FileAdapter::class);
//
//        $path = 'contents/6.txt';
//        $content = 'hello qiniu6';
//
//        // 上传内容到七牛
//        $adapter->write($path, $content);
//
//        // 获取私有链接
//        $url = $adapter->privateDownloadUrl($path);
//
//        return [
//            'url' => $url,
//            'content' => $adapter->read($url),
//        ];

//        $uids = [1, 2, 3];
//        return $this->userService->getMultiUserInfosFromCache($uids);
//        $id = $this->request->input('id');
//        return $this->userService->getUserInfoFromCache($id);

//        return $this->response->success($this->indexService->info());
//        $result = Db::select('SELECT * FROM email_code;');
//        return $result;

//        $id = $this->request->input('id');
//
//        $user = [
//            'id' => $id,
//            'name' => $id . '_' . 'name',
//        ];
//        $instance = JwtInstance::instance();
//        $instance->encode($user);
//
//        return [
//            'id' => $instance->id,
//            'user' => $instance->user,
//        ];

//        $a = $this->request->input('a');
//        Context::set('a', $a);
//
//        return [
//            'co_is' => Coroutine::inCoroutine(),
//            'co_id' => Coroutine::id(),
//            'a' => Context::get('a'),
//        ];

//        $a = $this->request->input('a');
//        if ($a) {
//            $this->a = $a;
//        }
//
//        return [
//            'co_is' => Coroutine::inCoroutine(),
//            'co_id' => Coroutine::id(),
//            'a' => $this->a,
//        ];

//        return convert_size(memory_get_usage(true));

//        $id = (int) $this->request->input('id', 0);
//        if ($id > 0) {
//            return $this->response->success(['info' => 'data info']);
//        } else {
//            return $this->response->fail(500, 'id无效');
//        }
//
//        return $this->response->success($this->indexService->info($id));

//        try {
//            return $this->response->success($this->indexService->info($id));
//        } catch (\Throwable $e) {
//            return $this->response->fail(500, $e->getMessage());
//        }
    }
}
