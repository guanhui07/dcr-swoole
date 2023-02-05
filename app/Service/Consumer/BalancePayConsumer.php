<?php

declare(strict_types=1);

namespace App\Service\Consumer;

use App\Model\UserModel;
use App\Utils\Json;
use App\Utils\Mq\MqConsumer;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class
 * @package app\service\consumer
 */
class BalancePayConsumer extends MqConsumer implements BaseConsumerInterface
{
    public static array $user = [];
    public static array $params = [];
    public string $desc = 'balance_pay消费者';
    //模块
    public string $module = 'balance_pay';

    public function __construct()
    {
        echo $this->desc . PHP_EOL;
    }


    /**
     * 消息处理
     * 模板方法模式
     * @param  $msg AMQPMessage
     */
    public function msgProc($msg)
    {
        echo $msg->body . PHP_EOL;
        $data = Json::decode($msg->body, true);
        $this->dealData($data);
        $msg->ack();
    }

    /**
     * @param $data array 从队列返回的数据（包含提交参数和处理的参数），返回完赋值相关使用变量，再一层校验穿参
     * websocket 推送 消息到 客户端
     * $data = [
     *    'params'=>$params,
     *    'user'=>$user,
     * ]
     *
     * @return bool
     */
    public function dealData(array $data): bool
    {
        self::$params = $params = $data['params'] ?? [];
        self::$user = $data['user'] ?? [];
        if (empty($params) || empty(self::$user) || !isset($params['nft_goods_id'])) {
            print_r($data);
            echo '数据有错误';
            return false;
        }

        $returnMessage = '恭喜您！购买成功，由于人数较多，请等待xx';
        $userInfo = UserModel::query()->where('id', $params['goods_id'])->where('status', 0)->first();
        if (!$userInfo) {
            $retError = errorx('不存在的xx信息，请重试') + ['module' => $this->module];
            return socketSend(self::$user['id'], $retError);
        }

        $ret = resultx([], 200, 'success', $this->module);
        return socketSend(self::$user['id'], $ret);
    }
}
