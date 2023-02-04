<?php

declare(strict_types=1);

use App\Middleware\TestMiddleware;
use DcrSwoole\Framework\Router;

// 用法类似 hyperf @see https://hyperf.wiki/3.0/#/zh-cn/quick-start/overview?id=%e9%80%9a%e8%bf%87%e9%85%8d%e7%bd%ae%e6%96%87%e4%bb%b6%e5%ae%9a%e4%b9%89%e8%b7%af%e7%94%b1
Router::get('/test_index','\App\Controller\TestController@index');
Router::post('/test_post_index','\App\Controller\TestController@index');
Router::addRoute(['GET'],'/','\App\Controller\TestController@index')->middleware(TestMiddleware::class);
Router::addRoute(['GET'],'/test1','\App\Controller\TestController@index');
Router::addRoute(['GET'],'/test','\App\Controller\TestController@index');
Router::addRoute(['GET'],'/test_response','\App\Controller\TestController@testResponse');
Router::addRoute(['GET'],'/test4','\App\Controller\TestController@index');
Router::addRoute(['GET'],'/trans','\App\Controller\TestController@trans');
Router::addRoute(['GET'],'/dto','\App\Controller\TestController@dto');
Router::addRoute(['GET'],'/token','\App\Controller\TestController@token');
Router::addRoute(['GET'],'/config_test','\App\Controller\TestController@config');
Router::addRoute(['GET'],'/guzzle','\App\Controller\TestController@guzzle');
Router::addRoute(['GET'],'/favicon.ico',function ($request, $response) {
        $response->end('');
    });

//return [];
//return Router::getRoutes();

