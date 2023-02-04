<?php

declare(strict_types=1);

namespace DcrSwoole\Framework;

use App\Provider\EventServiceProvider;
use App\Utils\ConfigBase;
use DcrSwoole\Event\EventInstance;
use DcrSwoole\Utils\ApplicationContext;
use Dotenv\Dotenv;
use itxiao6\SwooleDatabase\PDOConfig;
use itxiao6\SwooleDatabase\PoolManager;
use Raylin666\Guzzle\Client;
use Raylin666\Pool\PoolOption;
use Swoole\Database\RedisConfig;
use YiTin\TinRedis;

/**
 * 初始化 注册 各种 env config orm 门面 事件
 * 捕获异常，错误控制
 * Class Boostrap
 */
class Boostrap
{
    public function run(): void
    {
        $this->loadDotEnv();
        $config = di()->get(\DcrSwoole\Config\Config::class)->get('app');
        !defined('DEBUG') && define('DEBUG', $config['debug']); // online set false
        !defined('DCR_CONFIG') && define('DCR_CONFIG', $config);

//        date_default_timezone_set('PRC');
        date_default_timezone_set('Asia/Shanghai');


        if (DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
            ini_set('display_startup_errors', 'On');
        } else {
            error_reporting(0);
            ini_set('display_errors', 'Off');
            ini_set('display_startup_errors', 'Off');
        }

        set_exception_handler('cException');
        // laravel orm
        $this->bootstrapOrm();

        // 事件
        $this->loadEvents();

        // guzzle
        $this->bootGuzzle();
    }

    /**
     * @see https://github.com/illuminate/database
     * @see https://github.com/guanhui07/database
     */
    public function bootstrapOrm(): void
    {
        $configDb = di()->get(\DcrSwoole\Config\Config::class)->get('db');
        $configDb = $configDb['connections']['mysql'];

        (new PDOConfig())
            ->withDriver('mysql')
            ->withHost($configDb['hostname'])->
            withDbname($configDb['database'])
            ->withUsername($configDb['username'])
            ->withPassword($configDb['password'])
            ->withCharset('utf8mb4') // 字符集编码
            ->setConfig('default'); // 设置全局访问(默认为default)
        PoolManager::addPool(64, 'default'); // 设置指定连接池尺寸(连接名称默认为 default)
    }

    /**
     * boot Redis
     */
    public function bootRedis()
    {
        $config = config('redis', []);
        if (!empty($config)) {
            TinRedis::initialize(
                (new RedisConfig())
                ->withHost($config['host'])
                ->withPort($config['port'])
                ->withAuth($config['auth'])
                ->withDbIndex(1)
                ->withTimeout(1),
                $config['size'],// pool
            );
        }
    }

    /**
     * 加载dot env
     * @see https://github.com/vlucas/phpdotenv
     */
    protected function loadDotEnv(): void
    {
        $dotenv = Dotenv::createImmutable(PROJECT_ROOT);
        $dotenv->safeLoad();
    }

    /**
     * 事件
     * @see  https://www.doctrine-project.org/projects/doctrine-event-manager/en/latest/reference/index.html#setup
     */
    protected function loadEvents(): void
    {
        $eventManager = EventInstance::instance();
        $configs = EventServiceProvider::getEventConfig();

        foreach ($configs as $event => $listeners) {
            new $event($eventManager);
            $eventManager->addEventSubscriber(new $listeners());
        }
    }

    protected function bootGuzzle()
    {
        $container = ApplicationContext::getContainer();
        $client = new Client();
        $client->withPoolOption(
            (new PoolOption())->withMinConnections(1)
                ->withMaxConnections(10)
                ->withWaitTimeout(10)
        );
        $container->make(\GuzzleHttp\Client::class, [function () use ($client) {
            return $client->create();
        }]);
    }
}
