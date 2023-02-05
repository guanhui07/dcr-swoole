<?php

namespace App\Controller;

use App\Exception\LoginException;
use App\Exception\ParamsException;
use App\Exception\RuntimeException;
use App\Middleware\Contract\MiddlewareInterface;
use App\Middleware\Kernel;
use App\Model\UserModel;
use App\Traits\BaseRequest;
use App\Utils\Enviroment;
use App\Utils\JwtToken;
use DcrSwoole\Config\Config;
use DcrSwoole\Request\Request;
use DcrSwoole\Response\Response;
use DcrSwoole\Utils\DataRedis;
use Exception;
use GuzzleHttp\Client;
use Inhere\Validate\Validation;
use Middlewares\Utils\Dispatcher;
use DcrRedis\Redis;
use DI\Attribute\Inject;

class Controller
{
    use BaseRequest;

    public static null|array|string $user = []; // 用户token信息
    public static array $params = [];


    #[Inject]
    public Request $request;

    #[Inject]
    public Response $response;

    #[Inject]
    public Config $config;

    #[Inject]
    public Client $guzzleClient;

    #[Inject]
    public DataRedis $redis;

    public function __construct()
    {
//        $this->request = di()->get(Request::class);
//        $this->response = di()->get(Response::class);
//        $this->logger = di()->get(Log::class);
//        $this->config = di()->get(Config::class);
//        $this->guzzleClient = di()->get(\GuzzleHttp\Client::class);
//        $this->redis = di()->get(DataRedis::class);

        self::$params = (array)di()->get(Request::class)->get() + (array)di()->get(Request::class)->post();
//        if (isset(self::$params['token']) && Redis::get(stripslashes(self::$params['token']))) {
//            self::$user = Redis::get(stripslashes(self::$params['token']));
//        }

        $this->init();
    }

    /**
     * 初始化
     * @return bool
     */
    protected function init()
    {
        $token = self::$params['token'] ?? '';
        if ($token) {
            $userArr = JwtToken::decode($token);
            if (isset($userArr['id'])) {
                self::$user['id'] = $userArr['id'];
            }
        }
        if (Enviroment::isRoyeeDev()) {
            self::$user['id'] = 11211;
        }
        return true;
    }

    /**
     * @param $middleware
     *
     * @throws Exception
     * @see https://github.com/middlewares/utils
     */
    public function middleware($middleware)
    {
        $middlewares = Kernel::getMiddlewares();
        $arr = [];

        foreach ((array)$middleware as $m) {
            /** @var MiddlewareInterface $obj */
            $class = $middlewares[$m];
            $obj = (new $class());
            $arr[] = $obj->handle();
        }
        try {
            Dispatcher::run($arr + [
                    function ($request, $next) {
                        return $next->handle($request);
                    },
                ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 过滤params参数.
     * @param array $params 处理接口传过来参数数组
     * @param array|true $validators 验证器参数
     * @return array
     * @throws ParamsException
     */
    protected function paramsFilter($params, $validators = [])
    {
        $validator = Validation::check((array)$params, $validators);

        if ($validator->isFail()) {
            throw new ParamsException($validator->firstError());
        }

        return $params;
    }


    public function checkLogin()
    {
        if (Enviroment::isRoyeeDev()) {
            self::$user['id'] = 11211;
            return true;
        }

        if (!$this->getUserId()) {
            throw new LoginException('您还未登录，请先登录');
        }
        return $this->getUser();
    }

    public function getUserId()
    {
        return self::$user['id'] ?? 0;
    }

    public function getUser()
    {
        return UserModel::query()->find($this->getUserId());
    }

    public function debugSql()
    {
        echo UserModel::getLastSql();
        return;
    }
}
