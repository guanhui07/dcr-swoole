<?php

declare(strict_types=1);

namespace App\Traits;

use app\Utils\Str;
use DcrSwoole\Log\Log;
use DcrSwoole\Log\LogBase;
use DI\Attribute\Inject;
use itxiao6\SwooleDatabase\Adapter\Manager as DB;
use Qiniu\Auth as AuthQi;
use Qiniu\Storage\UploadManager;

trait BaseRequest
{
    /**
     * @var Log
     */
    #[Inject]
    protected Log $logger;

    //只用来调试
    public function log($v, $method = false): bool
    {
        if (!$method) {
            LogBase::info($v);
        } else {
            if (in_array($method, ['warning', 'error', 'info'])) {
                LogBase::$method($v);
            }
        }
        return true;
    }

    protected function succResponse($response = [], $bool = false)
    {
        return ([
            'status' => 200, 'message' => 'success', 'content' => $response,
        ]);
    }

    protected function errResponse($response)
    {
        return ([
            'status' => 0, 'message' => 'error', 'content' => $response,
        ]);
    }

    public function logFile($str, $file_name = '', $bool = false, $override = false): bool
    {
        if ($bool) {
            goto logfilebyname;
        }

        if (class_exists('SeasLog')) {
        } else {
            logfilebyname:
            if (!$file_name) {
                $file_name = runtime_path() . '/log/log.log';
            } else {
                $file_name = runtime_path() . '/log/' . $file_name;
            }
            if (is_object($str)) {
                $str = var_export($str, true);
            }
            file_put_contents($file_name, $str . "\r\n", $override ? 0 : FILE_APPEND);
        }
        return true;
    }

    public function dbLog($tmp): bool
    {
        $tmp = is_array($tmp) ? var_export($tmp, true) : $tmp;
        Db::table('t_test_log')->insert([
            'info_text' => var_export($tmp, true),
            //            'created_at'=>$this->timeFormat()
        ]);
        return true;
    }

    public function fetchAll($sql)
    {
        return Db::query($sql);
    }

    /**
     * @param string $filePath 或 图片url
     * @param string $bucket
     * @param string $qiniuFileUri
     *
     * @return false|string
     * @throws \Exception
     */
    public function uploadByQiniuGetUri($filePath = '/path/test.jpg', $bucket = 'nft-huanji', $qiniuFileUri = '')
    {
        //qiniu config
        //https://image.nft07.cn/goods/20220909/cast-card-1.png
        $cfg = [
            'access' => config('api.qiniu.access_key'),
            'secret' => config('api.qiniu.secret_key'),
            'bucket' => $bucket,
            'domain' => 'https://image.nft07.cn', //https://image.nft07.cn/
        ];

        ////qi-niu.test.com.qiniudns.com/FmiKrT2XfdiS3vtIqLasampii6vJ
        $auth = new AuthQi($cfg['access'], $cfg['secret']);
        //临时上传令牌
        $token = $auth->uploadToken($cfg['bucket'], null, 3600); //单位: s

        $uploadMgr = new UploadManager();
        if (!$qiniuFileUri) {
            $qiniuFileUri = date('Ymd') . '/'
                . md5(time() . mt_rand(1, 99999) . Str::random(16)) . '.png';
        }

        //$qiniu_file = md5(file_get_contents($filePath));

        if (!filter_var($filePath, FILTER_VALIDATE_URL)) {
            //[$qiniu_file, $err] =  Etag::sum($filePath);
            [$ret, $err] = $uploadMgr->putFile($token, $qiniuFileUri, $filePath);
        } else {
            [$ret, $err] = $uploadMgr->put($token, $qiniuFileUri, file_get_contents($filePath));
        }

        if ($err !== null) {
            if (!is_string($err)) {
                $err = var_export($err, true);
            }
            $this->logFile($err, 'qi_niu.log', true);
            //$this->err = $err;
            return false;
        }

        //echo $cfg['domain'] . '/' . $ret['key'];
        return '/' . $ret['key'];
    }
}
