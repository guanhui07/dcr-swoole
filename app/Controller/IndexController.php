<?php

declare(strict_types=1);

namespace App\Controller;

use App\Middleware\AuthMiddleware;
use App\Middleware\TestMiddleware;
use App\Service\TestService;

use DcrSwoole\Annotation\Mapping\Middlewares;
use DcrSwoole\Annotation\Mapping\RequestMapping;
use DcrSwoole\Request\Request;
use DI\Attribute\Inject;

class IndexController extends Controller
{
    #[Inject]
    public TestService $testService;

    /**
     * @param TestService $testService
     */
    public function __construct(TestService $testService)
    {
        parent::__construct();
//        $this->testService = $testService;
//        $this->middleware(TestMiddleware::class);
    }

    #[RequestMapping(methods: "GET , POST", path:"/index/test1")]
    public function test(): string
    {
        return 'hello world';
    }


    /**
     * 测试路由注解
     * 测试中间件注解
     * @return string
     */
    #[RequestMapping(methods: "GET , POST", path:"/index/test2")]
    #[Middlewares(AuthMiddleware::class, TestMiddleware::class)]
    public function test2(): string
    {
        return 'test 1121';
    }
}
