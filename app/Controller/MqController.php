<?php

namespace App\Controller;

use App\Utils\Mq\MqProducer;
use DcrSwoole\Annotation\Mapping\RequestMapping;
use Exception;

class MqController extends Controller
{
    /**
     * MqController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 测试使用 rabbitmq 生产者
     * @return array
     * @throws Exception
     */
    #[RequestMapping(methods: "GET , POST", path:"/test/mq")]
    public function test1()
    {
        $queueParam = ['test' => 111];
        $producer = new MqProducer();
        $producer->publish($queueParam, 'balance_pay', 1);
        return apiResponse([]);
    }
}
