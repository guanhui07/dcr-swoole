<?php

namespace App\Utils\Mq;

use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MqConsumer
{
    /**
     * 消费者
     * @see https://www.rabbitmq.com/tutorials/tutorial-five-php.html
     * @param $name string
     *
     * @throws Exception
     */
    public function consumer($name)
    {
        //获取配置
        $amqpConfig = di()->get(\DcrSwoole\Config\Config::class)->get('rabbitmq.AMQP');
        $amqpQueueDefailConfig = di()->get(\DcrSwoole\Config\Config::class)->get('rabbitmq.' . $name . '_queue');

        //连接
        $connection = new AMQPStreamConnection(
            $amqpConfig['host'],
            $amqpConfig['port'],
            $amqpConfig['username'],
            $amqpConfig['password']
        );

        //建立通道
        $channel = $connection->channel();

        //流量控制
        $channel->basic_qos(null, 1, null);

        //初始化交换机   交换机名 类型 topic
        /*
         * 创建交换机(Exchange)
         * name: vckai_exchange// 交换机名称
         * type: direct        // 交换机类型，分别为direct/fanout/topic，参考另外文章的Exchange Type说明。
         * passive: false      // 如果设置true存在则返回OK，否则就报错。设置false存在返回OK，不存在则自动创建
         * durable: false      // 是否持久化，设置false是存放到内存中的，RabbitMQ重启后会丢失
         * auto_delete: false  // 是否自动删除，当最后一个消费者断开连接之后队列是否自动被删除
         */
        $channel->exchange_declare($amqpQueueDefailConfig['exchange_name'], $amqpQueueDefailConfig['exchange_type'], false, true, false);

        //初始化队列
        /*
         * 创建队列(Queue)
         * name: hello         // 队列名称
         * passive: false      // 如果设置true存在则返回OK，否则就报错。设置false存在返回OK，不存在则自动创建
         * durable: true       // 是否持久化，设置false是存放到内存中RabbitMQ重启后会丢失,
         *                        设置true则代表是一个持久的队列，服务重启之后也会存在，因为服务会把持久化的Queue存放在硬盘上，当服务重启的时候，会重新加载之前被持久化的Queue
         * exclusive: false    // 是否排他，指定该选项为true则队列只对当前连接有效，连接断开后自动删除
         *  auto_delete: false // 是否自动删除，当最后一个消费者断开连接之后队列是否自动被删除
         */
        $channel->queue_declare($amqpQueueDefailConfig['queue_name'], false, true, false, false);

        //绑定队列与交换机 绑定路由routing_key
        /*
         * 绑定队列和交换机
         * @param string $queue 队列名称
         * @param string $exchange  交换器名称
         * @param string $routing_key   路由key
         * @param bool $nowait
         * @param array $arguments
         * @param int|null $ticket
         * @throws \PhpAmqpLib\Exception\AMQPTimeoutException if the specified operation timeout was exceeded
         * @return mixed|null
         */
        $channel->queue_bind($amqpQueueDefailConfig['queue_name'], $amqpQueueDefailConfig['exchange_name'], $amqpQueueDefailConfig['route_key']);

        //消费消息 回调
        /*
            queue: 从哪里获取消息的队列
            consumer_tag: 消费者标识符,用于区分多个客户端
            no_local: 不接收此使用者发布的消息
            no_ack: 设置为true，则使用者将使用自动确认模式。详情请参见.
                        自动ACK：消息一旦被接收，消费者自动发送ACK
                        手动ACK：消息接收后，不会发送ACK，需要手动调用
            exclusive:是否排他，即这个队列只能由一个消费者消费。适用于任务不允许进行并发处理的情况下
            nowait: 不返回执行结果，但是如果排他开启的话，则必须需要等待结果的，如果两个一起开就会报错
            callback: :回调逻辑处理函数,PHP回调 array($this, 'process_message') 调用本对象的process_message方法
        */
        $channel->basic_consume(
            $amqpQueueDefailConfig['queue_name'],
            $amqpQueueDefailConfig['consumer_tag'],
            false,
            false,
            false,
            false,
            [$this, 'msgProc']
        );

        //退出
        register_shutdown_function([$this, 'shutdown'], $channel, $connection);

        //监听
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }

    /**
     * 消息处理
     *
     * @param  $msg AMQPMessage
     */
    public function msgProc($msg)
    {
        echo $msg->body . "\n";
        $msg->ack();
    }

    /**
     * 退出
     *
     * @param  $channel AMQPChannel [信道]
     * @param $connection AMQPStreamConnection [连接] AMQPMessage
     *
     * @throws Exception
     */
    public function shutdown($channel, $connection)
    {
        $channel->close();
        $connection->close();
    }
}
