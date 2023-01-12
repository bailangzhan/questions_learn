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
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});

Router::post('/mail/getCode', 'App\Controller\MailController@getCode');
Router::post('/user/signup', 'App\Controller\UserController@signup');
Router::post('/user/login', 'App\Controller\UserController@login');
Router::get('/question/search', 'App\Controller\QuestionController@search');
Router::get('/question/{id:\d+}', 'App\Controller\QuestionController@info'); // 问题详情
Router::get('/question/list', 'App\Controller\QuestionController@list'); // 问题列表
Router::get('/question/hotRank', 'App\Controller\QuestionController@hotRank'); // 热门榜单

Router::addGroup('/auth', function () {
    Router::get('/user/test', 'App\Controller\UserController@test');
    Router::post('/question/save', 'App\Controller\QuestionController@save');
}, ['middleware' => [\App\Middleware\AuthMiddleware::class]]);
