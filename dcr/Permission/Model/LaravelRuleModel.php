<?php

/**
 * @desc Laravel Policy Model
 */

declare(strict_types=1);

namespace DcrSwoole\Permission\Model;

//use Illuminate\Database\Eloquent\Model;
use App\Model\PermissionModel;
use DcrRedis\Redis;
use DcrSwoole\DbConnection\Model;
use DcrSwoole\Utils\Coroutine;
use Guanhui07\SwooleDatabase\Adapter\DB;
use function Swoole\Coroutine\run;
use function Swoole\Coroutine\go;
/**
 * RuleModel Model
 */
class LaravelRuleModel extends Model
{
    protected $table = 'casbin_rule';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * a cache store.
     *
     */
    protected $store;

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = ['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'];

    /**
     * the guard for lauthz.
     *
     * @var string
     */
    protected string $guard;

    /**
     * 架构函数
     * @access public
     * @param array $data 数据
     */
    public function __construct(array $data = [])
    {
//        $connection = $this->config('database.connection') ?: config('database.default');
//        $this->setConnection($connection);
//        $this->setTable($this->config('database.rules_table'));
        parent::__construct($data);
    }

//    /**
//     * Gets config value by key.
//     *
//     * @param string|null $key
//     * @param null $default
//     *
//     * @return mixed
//     */
//    protected function config(string $key = null, $default = null)
//    {
//        $driver = config('permission.default');
//        return config('permission.' . $driver . '.' . $key, $default);
//    }

    /**
     * Gets rules from caches.
     *
     */
    public function getAllFromCache()
    {
//        $get = function () {
//            return $this->select('ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5')->get()->toArray();
//        };
//        if (!$this->config('cache.enabled', false)) {
//            return $get();
//        }

//        return $this->store->remember($this->config('cache.key'), $this->config('cache.ttl'), $get);
        if ($this->isCoroutine()) {
            var_dump('1111');
//            return DB::table('casbin_rule')->select(['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'])->get();
            return PermissionModel::query()->select(['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'])->get()->toArray();
        }
        var_dump(222);
//        return PermissionModel::query()->select(['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'])->get()->toArray();


//        go(static function(){
//            $data = PermissionModel::query()->select(['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'])->get()->toArray();
//            Redis::set('casbin_cache',$data,60);
//        });

        $data = PermissionModel::query()->select(['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'])->get()->toArray();

//        \Swoole\Event::wait();
        return $data;

    }

    protected function isCoroutine(): bool
    {
        return Coroutine::id() > 0;
    }
}
