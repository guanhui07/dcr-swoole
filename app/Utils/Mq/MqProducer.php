<?php

namespace App\Utils\Mq;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MqProducer
{
    /**
     * 生产者
     * @see https://www.rabbitmq.com/tutorials/tutorial-five-php.html
     * @param array $data
     * @param string $key
     * @param int $isRetry 是否是重试
     *
     * @return bool
     * @throws Exception
     */
    public function publish($data, $key, $isRetry = 0)
    {
        //获取amqp连接配置
        $amqpConfig = di()->get(\DcrSwoole\Config\Config::class)->get('rabbitmq.AMQP');
        // 交换机exchange 路由routing_key 队列 queue 配置
        $amqpQueueDefailConfig = di()->get(\DcrSwoole\Config\Config::class)->get('rabbitmq.' . $key . '_queue');
        //连接
        $connection = new AMQPStreamConnection(
            $amqpConfig['host'],
            $amqpConfig['port'],
            $amqpConfig['username'],
            $amqpConfig['password']
        );

        //        if ($isRetry) {
        //            // 重试交换机
        //            $data['retry-count'] =  $data['retry-count'] +1 ?? 1;
        //            $amqpQueueDefailConfig['exchange_name'] .= '.retry';
        //        }

        //建立通道
        $channel = $connection->channel();

        //初始化交换机  交换机名 类型 topic
        /*
         * 创建交换机(Exchange)
         * name: vckai_exchange// 交换机名称
         * type: topic        // 交换机类型，分别为direct/fanout/topic，参考另外文章的Exchange Type说明。
         * passive: false      // 如果设置true存在则返回OK，否则就报错。设置false存在返回OK，不存在则自动创建
         * durable: false      // 是否持久化，设置false是存放到内存中的，RabbitMQ重启后会丢失
         * auto_delete: false  // 是否自动删除，当最后一个消费者断开连接之后队列是否自动被删除
         */
        $channel->exchange_declare($amqpQueueDefailConfig['exchange_name'], $amqpQueueDefailConfig['exchange_type'], false, true, false);

        //生成消息
        $msg = new AMQPMessage(json_encode($data), [
            'content-type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        ]);

        //推送消息到某个交换机 绑定路由routing_key
        $channel->basic_publish($msg, $amqpQueueDefailConfig['exchange_name'], $amqpQueueDefailConfig['route_key']);

        $channel->close();
        $connection->close();

        return true;
    }
}
