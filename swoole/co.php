<?php

//function say($s) {
//    \Swoole\Coroutine::sleep(0.001);
//    for ($i = 0; $i < 5; $i ++) {
//        print_r($s . "\n");
//    }
//}
function say($s) {
    Swoole\Coroutine::sleep(0.001);
    return $s;
}

//
//say("hello");
//say("world");

//\Swoole\Coroutine\run(function () {
//    // 子协程
//    Swoole\Coroutine::create(function () {
//        \Swoole\Coroutine::sleep(.001);
//        say("hello, 协程id=" . \Swoole\Coroutine::getCid() . ", 父协程id=" . Swoole\Coroutine::getPcid());
//    });
//
//    // 主协程
//    say("world, 协程id=" . \Swoole\Coroutine::getCid());
//});

//Swoole\Coroutine\run(function () {
//    $result = [];
//    // 子协程①
//    Swoole\Coroutine::create(function () use (&$result) {
//        $result[] = say("hello");
//    });
//
//    // 子协程②
//    Swoole\Coroutine::create(function () use (&$result) {
//        $result[] = say("world");
//    });
//
//    Swoole\Coroutine::sleep(0.001);
//
//    // 主协程
//    var_dump($result);
//});

//Swoole\Coroutine\run(function () {
//    $result = [];
//    $wg = new Swoole\Coroutine\WaitGroup();
//    $wg->add(2);
//
//    // 子协程①
//    Swoole\Coroutine::create(function () use ($wg, &$result) {
//        Swoole\Coroutine\defer(function () use ($wg) {
//            $wg->done();
//        });
//        $result[] = say("hello");
//    });
//
//    // 子协程②
//    Swoole\Coroutine::create(function () use ($wg, &$result) {
//        Swoole\Coroutine\defer(function () use ($wg) {
//            $wg->done();
//        });
//
//        $result[] = say("world");
//    });
//
//    // 主协程
//    $wg->wait();
//    var_dump($result);
//});
//
//Swoole\Coroutine\run(function () {
//    $result = [];
//    $channel = new \Swoole\Coroutine\Channel(1);
//
//    // 子协程①
//    Swoole\Coroutine::create(function () use ($channel) {
//        $channel->push(say("hello"));
//    });
//
//    // 子协程②
//    Swoole\Coroutine::create(function () use ($channel) {
//        $channel->push(say("world"));
//    });
//
//    for($i = 0; $i < 2; $i ++) {
//        $result[] = $channel->pop();
//    }
//    // 主协程
//    var_dump($result);
//});


//Swoole\Coroutine\run(function () {
//    $channel = new \Swoole\Coroutine\Channel();
//
//    // 子协程
//    Swoole\Coroutine::create(function () use ($channel) {
//        var_dump("协程任务执行[child]①");
//        // Coroutine::sleep 模拟任务执行
//        \Swoole\Coroutine::sleep(1);
//        var_dump("协程结束执行[child]②");
//
//        $channel->push(say("finish[by child]③"));
//
//        var_dump("协程完成[child]④");
//
//    });
//
//    var_dump("协程开始[main]⑤");
//
//    $result = $channel->pop();
//
//    // 主协程
//    var_dump("{$result}[main]⑥");
//});

//Swoole\Coroutine\run(function () {
//    try {
//        $channel = new \Swoole\Coroutine\Channel();
//
//        // 子协程
//        Swoole\Coroutine::create(function () use ($channel) {
//            try {
//                throw new \Exception("exception...");
//            } catch (\Throwable $e) {
//                $channel->push($e);
//            }
//        });
//
//        $result = $channel->pop();
//        if ($result instanceof \Throwable) {
//            throw $result;
//        }
//    } catch (\Throwable $e) {
//        var_dump($e->getMessage());
//    }
//});

Swoole\Coroutine\run(function () {
    try {
        // 子协程
        Swoole\Coroutine::create(function () {
            throw new \Exception("exception...");
        });
    } catch (\Throwable $e) {
        var_dump($e->getMessage());
    }
});
