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

class MiddlewareController extends Controller
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

    public function index()
    {
        return 'hello world';
    }


    #[RequestMapping(methods: "GET , POST", path:"/test/middleware")]
    #[Middlewares(AuthMiddleware::class, TestMiddleware::class)]
    public function test()
    {
        return 'ok';
    }
}
