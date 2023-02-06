<?php

use App\Utils\Json;
use DcrSwoole\Event\EventInstance;
use DcrSwoole\Utils\ApplicationContext;
use Doctrine\Common\EventArgs;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use DcrRedis\Redis;

if (!function_exists('debug')) {
    function debug($v)
    {
        if (is_string($v) || is_int($v)) {
            if (trim($v) === '') {
                var_dump($v);
                return;
            }
            echo $v;
        } elseif (is_bool($v) || is_resource($v) || is_null($v) || is_object($v)) {
            var_dump($v);
        } elseif (is_array($v)) {
            print_r($v);
        } else {
            var_dump($v);
        }
    }
}

function apiReturnSuccess($outputData = []): string
{
    $data['code'] = 200;
    $data['message'] = 'success';
    $data['data'] = $outputData;
    $urldecode_flag = false;
    return Json::encode($data, $urldecode_flag);
}


function runtime_path(): string
{
    return PROJECT_ROOT . '/runtime/';
}


function cException($exception)
{
    if (DEBUG === true) {
        echo $exception->__toString();
        return;
    }

//    $request = Request::instance();
    //record exception log
//    Logger::exception(basename($exception->getFile()),
    // $exception->getFile(), $exception->getLine(),
//        $exception->getMessage(), $exception->getCode(), $exception->getTraceAsString(), array(
//        "action"    => $request->header("HOST") . $request->server("REQUEST_URI"),
//        "server_ip" => gethostname(),
//    ));
    $log_data = '';
    $log_data .= date("Y-m-d H:i:s") . ' ' . $exception->__toString();

    writeLog($log_data, 'exception_error');
    echo $log_data;
    return;
}


function writeLog($msg, $name = null, $logDir = null)
{
    if (!$name) {
        $name = date('Y-m-d_H', time());
    } else {
        if ($logDir === null) {
            $name .= '_' . date('H', time());
        }
    }
    if (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR']) {
        $name .= '_' . $_SERVER['SERVER_ADDR'];
    } else {
        if (isset($GLOBALS['local_ip'])) {
            $name .= '_' . $GLOBALS['local_ip'];
        }
    }
    if ($logDir === null) {
        $logDir = '/' . 'pre_' . '/' . date('Ym', time()) . '/' . date('d', time());
    }

    if (!file_exists(PROJECT_ROOT . 'runtime/errlog/' . $logDir)) {
        mkdir(PROJECT_ROOT . 'runtime/errlog/' . $logDir, 0777, true);
    }

    $logPath = PROJECT_ROOT . 'runtime/errlog/' . $logDir;
    $logFile = $logPath . "/" . $name . ".log";

    if (is_array($msg)) {
        $msg = json_encode($msg);
    }
    $msg = '[' . date("Y-m-d H:i:s", time()) . '] ' . $msg . "\n";

    return file_put_contents($logFile, $msg, FILE_APPEND);
}


function getIP()
{
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    }

    preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
    $onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : null;
    unset($onlineipmatches);
    return $onlineip;
}


function curlGetContents($url, $timeout = 3)
{
    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0); // 让CURL支持HTTPS访问
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
    $result = curl_exec($curlHandle);
    curl_close($curlHandle);
    return $result;
}


function curlPostContents($url, $params, $use_http_build_query = true)
{
    if ($use_http_build_query) {
        $params = http_build_query($params);
    }

    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_POST, 1);
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0); // 让CURL支持HTTPS访问
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $params);
    $result = curl_exec($curlHandle);
    curl_close($curlHandle);
    return $result;
}


function apiResponse($data, $msg = 'sucess')
{
    $ret = [
        'msg' => $msg,
        'code' => 0,
        'data' => $data,
    ];
    return ($ret);
}


function apiResponseError($msg = 'error', $code = 11211, $data = [])
{
    $ret = [
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ];
    return ($ret);
}


function mkDirRev($path): bool
{
    if (is_dir($path)) {
        return true;
    }
    return is_dir(dirname($path)) || mkDirRev(dirname($path)) ? mkDirRev($path) : false;
}


function signatureKey($param): string
{
    ksort($param);
    $string = '';
    foreach ($param as $k => $v) {
        $string .= $k . '=' . urlencode($v);
    }
    $newTicket = hash_hmac("md5", strtolower($string), 'pushOrder');
    return $newTicket;
}


function randStr(): string
{
    $arr = array_merge(range(0, 9), range("a", "z"), range("A", "Z"));
    shuffle($arr);
    $arr2 = array_slice($arr, 0, 4);
    return implode('', $arr2);
}


function xmlToArray($xml)
{
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $array_data;
}


if (!function_exists('objToArray')) {
    function objToArray($o)
    {
        // return json_decode(  json_encode($o),1 );
        return json_decode(json_encode($o), true);
    }
}

// 统一封装API接口结果输出
function result($data, $code = 200, $message = '成功'): string
{
    $name = 'content';
    if ($code == false) {
        $json = $data;
    } else {
        $json['status'] = $code;
        $json['message'] = $message;
        $data ? $json[$name] = $data : '';
    }
    // 开启api记录
    apiLog($json);
    if ($code == false) {
        print_r($data);
        return $data;
    } else {
        return Json::encode($json);
    }
}


function resultx($data, $code = 200, $message = '成功', $module = '')
{
    $name = 'content';

    if ($code == false) {
        $json = $data;
    } else {
        $json['status'] = $code;
        $json['message'] = $message;
        $json['module'] = $module;
        $data ? $json[$name] = $data : '';
    }
    // 开启api记录
    apiLog($json);
    print_r($json);
    echo PHP_EOL;
    return $json;
}


// 统一封装API接口错误输出
function error($message = '失败', $code = 404, $data = ''): string
{
    $name = 'content';
    if ($code == false) {
        $json = $data;
    } else {
        $json['status'] = $code;
        $json['message'] = $message;
        $data ? $json[$name] = $data : '';
    }
    // 开启api记录
    if ($code == false) {
        print_r($data);
        return $message;
    } else {
        $result = json_encode($data);
        return Json::encode($result);
    }
}


function errorx($message = '失败', $code = 404, $data = '')
{
    $name = 'content';
    if ($code == false) {
        $jsonArr = $data;
    } else {
        $jsonArr['status'] = $code;
        $jsonArr['message'] = $message;
        $data ? $jsonArr[$name] = $data : '';
    }
    print_r($jsonArr);
    // 开启api记录
    apiLog($jsonArr);
    return $jsonArr;
}


if (!function_exists('cache')) {
    function cache($key, Closure $closure, $ttl = null)
    {
        if ($result = Redis::get($key)) {
            return unserialize($result);
        }
        $result = $closure($ttl);
        Redis::set($key, serialize($result), $ttl);
        return $result;
    }
}


if (!function_exists('collectNew')) {
    /**
     * @param null|array|Collection $value
     * @param bool $toLine
     *
     * @return Collection
     */
    function collectNew($value = null, $toLine = false)
    {
        if (empty($value)) {
            return new Collection([]);
        }
        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }
        if ($value instanceof Collection) {
            return $value;
        }
        //        $value = is_array($value) && $toLine ? arrayToLine($value) : $value;
        return new Collection($value);
    }
}


if (!function_exists('retry')) {
    /**
     * $data = retry(3, function () {
     * $rand = mt_rand(1, 10);
     * if ($rand > 8) {
     * return $rand;
     * } else {
     * throw new \Exception('test',1);
     * }
     * }, 5);
     * var_dump($data);
     *
     * @param int $times 重试次数
     * @param callable $callback 闭包
     * @param int $sleep 阻塞秒数
     *
     * @return mixed
     * @throws Throwable
     */
    function retry(int $times, callable $callback, int $sleep = 0): mixed
    {
        beginning:
        try {
            return $callback();
        } catch (\Throwable $e) {
            echo $times . PHP_EOL;
            if (--$times < 0) {
                throw $e;
            }
            sleep($sleep);
            goto beginning;
        }
    }
}

function real_ip()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all(
        '#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s',
        $_SERVER['HTTP_X_FORWARDED_FOR'],
        $matches
    )) {
        foreach ($matches[0] as $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    return $ip;
}


function di($name = null)
{
    $container = ApplicationContext::getContainer();
    if ($name === null) {
        return $container;
    }
    return $container->get($name);
}
if (!function_exists('containerNew')) {
    function containerNew($name)
    {
        return di($name);
    }
}

function event($event,string $eventName = ''): bool
{
    $dispatcher = EventInstance::instance();
    $dispatcher->dispatch($event, $eventName);
    return true;
}


/**
 * 转换数组key成下划线
 *
 * @param array $array
 *
 * @return array
 */
function arrayToLine(array $array): array
{
    if (empty($array)) {
        return [];
    }
    if ($array instanceof Arrayable) {
        $array = $array->toArray();
    }

    $convert = [];
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $convert[is_string($key) ? stringToLine($key) : $key] = arrayToLine($value);
        } else {
            $convert[is_string($key) ? stringToLine($key) : $key] = $value;
        }
    }

    return $convert;
}


/**
 * 驼峰命名转下划线
 *
 * @param string $string 要转换的字符串
 */
function stringToLine(string $string): string
{
    $replaceString = preg_replace_callback('/([A-Z])/', function ($matches) {
        return '_' . strtolower($matches[0]);
    }, $string);

    return trim(preg_replace('/_{2,}/', '_', $replaceString), '_');
}


if (!function_exists('array_multi_column')) {
    function array_multi_column(array $array, array $column): array
    {
        return array_map(function ($data) use ($column) {
            return array_intersect_key($data, array_flip(array_values($column)));
        }, $array);
    }
}

if (!function_exists('call')) {
    /**
     * Call a callback with the arguments.
     *
     * @param mixed $callback
     * @param array $args
     *
     * @return mixed
     */
    function call($callback, array $args = []): mixed
    {
        $result = null;
        if ($callback instanceof Closure) {
            $result = $callback(...$args);
        } elseif (is_object($callback) || (is_string($callback) && function_exists($callback))) {
            $result = $callback(...$args);
        } elseif (is_array($callback)) {
            [$object, $method] = $callback;
            $result = is_object($object) ? $object->{$method}(...$args) : $object::$method(...$args);
        } else {
            $result = call_user_func_array($callback, $args);
        }
        return $result;
    }
}

/**
 * @desc arraySort php二维数组排序 按照指定的key 对数组进行排序
 *
 * @param array $arr 将要排序的数组
 * @param string $keys 指定排序的key
 * @param string $type 排序类型 asc | desc
 *
 * @return array
 */
function arraySort(array $arr, string $keys, $type = 'asc'): array
{
    $keysvalue = $new_array = [];
    foreach ($arr as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    $type === 'asc' ? asort($keysvalue) : arsort($keysvalue);
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}


/**
 * 获取毫秒级别的时间戳
 */
function getTimeMillisecond()
{
    // 获取毫秒的时间戳
    [$msec, $sec] = explode(' ', microtime());
    return (float)sprintf('%.0f', ((float)$msec + (float)$sec) * 1000);
}


/**
 * 对象类型转换entity
 *
 * @param Object $std
 * @param stdClass $entity
 *
 * @return stdClass
 */
function classToEntity(object $std, stdClass $entity): stdClass
{
    foreach ($entity as $property => $value) {
        if (isset($std->$property)) {
            $entity->$property = $std->$property;
        }
    }
    return $entity;
}


/**
 * 数组类型转换entity
 *
 * @param array $array
 * @param object $entity
 *
 * @return object
 */
function arrayToEntity(array $array, object $entity): object
{
    foreach ($entity as $property => $value) {
        if (isset($array[$property])) {
            $entity->$property = $array[$property];
        }
    }
    return $entity;
}


function configNew(string $name)
{
    return di()->get(\DcrSwoole\Config\Config::class)->get($name);
}


function createDirectoryIfNeeded($directory)
{
    if (!file_exists($directory) || !is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
}
