<?php
// 配置文件
return [
    // 连接信息
    'AMQP' => [
        'host' => $_ENV['mq_host'],   //连接rabbitmq,此为安装rabbitmq服务器
        'port' => '5672',
        'username' => 'guest',
        'password' => 'guest',
        'vhost' => '/',
    ],

    // 订单队列
    'order_queue' => [
        'exchange_name' => 'topic_exchange',
        'exchange_type' => 'topic',
        'queue_name' => 'order_queue',
        'route_key' => '*.order',
        'consumer_tag' => 'order',
    ],
    // /mq/test1
    'balance_pay_queue' => [
        'exchange_name' => 'topic_exchange',
        'exchange_type' => 'topic',
        'route_key' => 'balance_pay.exchange',
        'queue_name' => 'balance_pay.exchange.balance_pay_queue',
        'consumer_tag' => 'balance_pay',
    ],
];