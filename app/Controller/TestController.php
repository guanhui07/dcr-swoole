<?php

declare(strict_types=1);

namespace App\Controller;

use app\Event\TestEvent;
use App\Middleware\TestMiddleware;
use App\Model\GoodModel;
use App\Model\UserModel;
use App\Repository\TestRepository;
use App\Service\Entity\ExchGiftInfo;
use App\Service\Entity\TestEntity;
use App\Service\TestService;
use App\Utils\Json;
use App\Utils\JwtToken;
use Carbon\Carbon;
use DcrSwoole\Annotation\Mapping\RequestMapping;
use DcrSwoole\Log\LogBase;
use DcrSwoole\Request\Request;
use DcrSwoole\Utils\ApplicationContext;
use DI\Attribute\Inject;
use Inhere\Validate\Validation;
use itxiao6\SwooleDatabase\Adapter\Manager as DB;
use YiTin\TinRedis;

class TestController extends Controller
{
    /**
     * @see https://php-di.org/doc/attributes.html
     *  php8注解方式注入
     * @var TestService
     */
    #[Inject]
    public TestService $testService;

    /**
     * 测试测试参数依赖注入
     * 测试使用中间件
     *
     * @param TestService $testService
     */
    public function __construct(TestService $testService)
    {
        parent::__construct();
//        $this->testService = $testService;
        $this->middleware(TestMiddleware::class);
    }

    #[RequestMapping(methods: "GET , POST", path:"/test/request")]
    public function testRequest()
    {
//       return $this->request->all();
//       return $this->request->get();
        return $this->request->post('name', 'test');
    }

    /**
     * response
     * @return int[]
     */
    #[RequestMapping(methods: "GET , POST", path:"/test/response")]
    public function testResponse()
    {
//       return $this->response->end([1,2]);
        return [1, 2];
    }

    /**
     * 测试redis
     * @return array
     */
    #[RequestMapping(methods: "GET , POST", path:"/test/test_redis")]
    public function testRedis()
    {
        $this->redis->setex('test_key1', 23, 'test111');
        var_dump($this->redis->get('test_key1'));

        TinRedis::setex('test_key', 22, 'test redis222');
//        var_dump(TinRedis::get('test_key'));
        return [];
    }

    /**
     * test demo
     * @param $request \Swoole\Http\Request
     * @return bool
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @see https://wiki.swoole.com/#/http_server?id=httprequest
     */
    #[RequestMapping(methods: "GET , POST", path:"/test/index")]
    public function index($request)
    {
//        return $this->request->fullUrl();
        $code = $this->request->input('code');
//        return apiResponse([]);
        $params = ApplicationContext::getContainer()->get(Request::class)->all();
        $this->logger->info('fggg');
        ApplicationContext::getContainer()->get(TestService::class)->testDi();
//        $this->checkLogin();
        LogBase::info('abc');
        di(TestRepository::class)->test1();
        // 测试request
//        var_dump($request->get['hello'] ?? 'null');
        var_dump($this->request->get['hello'] ?? 'hello world');
        var_dump($request->post['hello'] ?? 'hello null');
        //测试获取config
//        var_dump( configNew('app.debug'));

        $allProject = DB::table('user')->where('id', '>', 1)
            ->orderBy('id', 'desc')->limit(2)->get(['id']);
//        print_r($allProject->toArray());

        // 测试collect
        $test = collect([23, 34])->pop();
        // 测试carbon
        echo Carbon::now()->format('Y-m-d');
        // 测试 orm包
        $test = UserModel::query()->orderBy('id', 'desc')->limit(1)->get(['id']);
        var_dump($test->toArray());

        // 测试 DI
        $this->testService->testDi();

        //测试redis
        TinRedis::setex('test_key', 22, 'test redis');
//        var_dump(TinRedis::get('test_key'));
        return $this->response->end([
            'method' => $request->server['request_method'],
            'message' => $allProject,
        ]);
    }

    #[RequestMapping(methods: "GET , POST", path:"/test/hello")]
    public function hello($request, $response, $data)
    {
        return collect([1, 2]);
        $name = $data['name'] ?? 'DcrSwoole';

//        return $this->response->end( [
//            'method' => $request->server['request_method'],
//            'message' => "Hello {$name}.",
//        ]);

//        return  [
//            'method' => $request->server['request_method'],
//            'message' => "Hello {$name}.",
//        ];

        return $name;
    }

    /**
     * 测试Validate
     * @see https://github.com/inhere/php-validate
     */
    #[RequestMapping(methods: "GET , POST", path:"/test/test4")]
    public function test4($request, $response)
    {
        $validate = Validation::check($this->request->post ?? [], [
            // add rule
            ['title', 'min', 40],
            ['freeTime', 'number'],
        ]);

        if ($validate->isFail()) {
            var_dump($validate->getErrors());
            var_dump($validate->firstError());
        }

        // $postData = $v->all(); // 原始数据
        $safeData = $validate->getSafeData(); // 验证通过的安全数据

        return $response->end(
            Json::encode(
                $safeData
            )
        );
    }

    /**
     * 测试 dto
     */
    #[RequestMapping(methods: "GET , POST", path:"/test/dto")]
    public function dto($request, $response)
    {
        arrayToEntity([
            "msg" => "dsfdf",
            "user_id" => 222,

        ], new TestEntity());

        $settleConfig = new TestEntity([
            "msg" => "dsfdf",
            "user_id" => 222,
            'gift' => new ExchGiftInfo([
                'id' => 1,
                'name' => 'test',
            ]),
        ]);
        $id = $this->dtoParam($settleConfig);
        return $id;
    }

    protected function dtoParam(TestEntity $testEntity)
    {
        return $testEntity->gift->id;
    }

    #[RequestMapping(methods: "GET , POST", path:"/test/config")]
    public function config()
    {
        return $this->config->get('app.debug');
    }

    /**
     * 测试事件
     * @see  https://github.com/inhere/php-event-manager
     */
    #[RequestMapping(methods: "GET , POST", path:"/test/event")]
    public function event($request, $response)
    {
        $params = [
            'test' => 23,
        ];

        event(TestEvent::preFoo, $params);
        return [];
    }

    /**
     * 测试guzzle
     * @param $request
     * @param $response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    #[RequestMapping(methods: "GET , POST", path:"/test/guzzle")]
    public function guzzle($request, $response)
    {
//        $client = di()->get(\GuzzleHttp\Client::class);
        $client = $this->guzzleClient;
        $result = $client->get('http://127.0.0.1:9501/test1');
        $ret = $result->getBody()->getContents();
        $result->getBody()->close();

        return 'test '.$ret;
    }

    #[RequestMapping(methods: "GET , POST", path:"/test/trans")]
    public function trans($request, $response)
    {
        DB::transaction(function () {
            UserModel::query()->where('id', 10000)->increment('open_box');
            UserModel::query()->where('id', 10000)->increment('open_box');
        });
        return [];
    }

    /**
     * collect test
     * map transform each shufle slice
     * @return array
     */
    #[RequestMapping(methods: "GET , POST", path:"/test/collect")]
    public function collectTest()
    {
        $params = $this->paramsFilter($this->request->all(), [
            ['goods_id', 'required', "msg" => '必填参数不能为空'],
        ]);
        $perPage = $params['per_page'] ?? 10;
        $page = $params['page'] ?? 1;
        $moduleId = (int)$params['id'];
        $cacheKey = 'index:goods:guess_goods:';
        if ($cacheData = $this->redis->get($cacheKey . $params['goods_id'])) {
            return collect(Json::decode($cacheData, true))->shuffle()->toArray();
        }

        $goodsList = $stocks = GoodModel::query()->limit(2)->get();
        $stocks = $stocks->keyBy('stock_id');

        $stocksSort = array_column($stocks->toArray(), 'ordering', 'stock_id');

        $ret = collect($goodsList)->transform(function ($goods) use ($stocks) {
            $customName = $stocks[$goods['stock_id']]['custom_name'] ?? '';
            $customSelling = $stocks[$goods['stock_id']]['custom_selling'] ?? '';
            $customPicture = $stocks[$goods['stock_id']]['custom_picture'] ?? '';
            $goods['name'] = $customName ? $customName : $goods['name'];
            $goods['selling_point'] = $customSelling ?: $goods['selling_point'];
            $goods['has_custom_picture'] = false;
            if ($customPicture) {
                $goods['image'] = $customPicture;
                $goods['has_custom_picture'] = true;
            }
            return $goods;
        })->sortByDesc(function ($goods) use ($stocksSort) {
            return $stocksSort[$goods['stock_id']] ?? 0;
        })->values()->toArray();


        $data = $this->testService->test($this->request->all());
        $data['list'] = collect($data['list'])->map(function ($item) {
            $item['start_time'] = date('Y.m.d', strtotime($item['start_time']));
            $item['end_time'] = date('Y.m.d', strtotime($item['end_time']));
            return $item;
        });

        $dataNew = [];
        $data = UserModel::query()->forPage($page, $perPage)->get(['id', 'created_at']);
        $data->each(function (UserModel $detailItem) use (&$dataNew) {
            $dataNew[] = [
                'user_id' => $detailItem->id . ':test',
                'create_time' => $detailItem->created_at,
            ];
        });
        $result = collect($dataNew)->where('create_time', '>', '2020-2-19')->shuffle()->slice(0, 10)->toArray();
        $this->redis->setex($cacheKey . $params['goods_id'], 60, Json::encode($result));
        return $result;
    }

    /**
     * jwt 测试
     * @param $request
     * @param $response
     * @return array
     */
    #[RequestMapping(methods: "GET , POST", path:"/test/token")]
    public function token($request, $response)
    {
        $token = JwtToken::encode([
            'uid' => 27,
            'name' => 'test',
        ]);
//        $token = '1813bef4c03caef6ec45380a7246d110';
        $data = JwtToken::decode($token);
        return $data;
    }

    /**
     * @param $request
     * @param $response
     * @return array
     * @see  https://wiki.swoole.com/#/http_server?id=files
     */
    #[RequestMapping(methods: "GET , POST", path:"/test/upload")]
    public function upload($request, $response)
    {
        return [];
    }
}
