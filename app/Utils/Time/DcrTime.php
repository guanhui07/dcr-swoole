<?php

namespace App\Utils\Time;

/**
 * 时间的处理
 **/
class DcrTime
{
    /**
     * 格式化时间
     * @param int $t 时间戳
     * @param string $f 格式化格式
     *
     * @return string
     */
    public static function formatTime($t, $f = 'Y-m-d')
    {
        $m = empty($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] - $t : $t;
        if ($m <= 60) {
            return '刚刚';
        }

        if ($m > 60 and $m <= 3600) {
            $m = ceil($m / 60);
            return $m . '分钟前';
        }

        if ($m > 3600 and $m <= 86400) {
            $m = ceil($m / 3600);
            return $m . '小时前';
        }

        if ($m > 86400 and $m <= 2592000) {
            $m = ceil($m / 86400);
            return $m . '天前';
        }

        $m = ceil($m / (30 * 3600 * 24));
        return $m . '月前';
    }


    /**
     * 时间格式化
     *
     * @param
     * @param
     * @return int
     * */
    public static function secToTime($lastTime)
    {
        $period = time() - ((is_numeric($lastTime)) ? $lastTime : strtotime($lastTime));
        if ($period < 0) {
            return "刚刚发布";
        }

        if ($period < 60) {
            return ($period <= 0 ? 1 : $period) . "秒前";
        }

        if ($period < 3600) {
            return round($period / 60, 0) . "分钟前";
        }

        if ($period < 86400) {
            return round($period / 3600, 0) . "小时前";
        }

        if ($period < 86400 * 30) {
            return date('n月d日 H:i', $lastTime);
        }

        return date('n月d日 H:i', $lastTime);
    }

    /**
     * 计算时间返回当前时间秒数
     * @return float
     */
    public static function getTime()
    {
        //list($usec, $sec) = explode(" ", microtime());
        return microtime(true);
    }

    /**
     * 获取时间的秒数
     * @param string $time 时:分:秒
     * @return int
     */
    public static function getSecondTime($time = '01:01:01')
    {
        if (empty($time)) {
            return 0;
        }
        $times = explode(':', $time);
        $timer = 0;
        if (count($times) === 3) {
            $timer = (int)$times[0] * 3600 + (int)$times[1] * 60 + $times[2];
        }
        return $timer;
    }

    /**
     * 根据时间搓获取格式化后的时间
     * @param int $time
     * @return string
     */
    public static function getFormatSecondTime($time = 600)
    {
        if (empty($time)) {
            return false;
        }
        $h = floor($time / 3600);
        $hm = $time % 3600;
        $i = floor($hm / 60);
        $m = $hm % 60;
        $h = ($h < 10) ? '0' . $h : $h;
        $i = ($i < 10) ? '0' . $i : $i;
        $m = ($m < 10) ? '0' . $m : $m;
        return "{$h}:{$i}:{$m}";
    }

    /**
     * 获取时间戳
     * @param string $date
     * @return int
     */
    public function getUnixTimeStamp($date = "00-00-00 00:00:00")
    {
        if (!$date) {
            return 0;
        }
        [$d, $t] = explode(" ", $date);
        [$year, $month, $day] = explode("-", $d);
        [$hour, $minute, $second] = explode(":", $t);
        return mktime($hour, $minute, $second, $month, $day, $year);
    }

    /**
     * 日期格式--1 2015/06/07 15:30:23
     * @param int $type 0-今天开始，1-今天结束
     *
     * @return bool|string
     */
    public static function getTodayTime($type = 0)
    {
        $arr = getdate();
        return !$type ? mktime(00, 00, 00, $arr['mon'], $arr['mday'], $arr['year']) : mktime(23, 59, 59, $arr['mon'], $arr['mday'], $arr['year']);
    }
}
