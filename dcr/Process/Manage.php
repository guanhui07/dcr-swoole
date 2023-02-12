<?php


namespace DcrSwoole\Process;


use DcrSwoole\Log\LogBase;
use Swoole\Process;
use Swoole\Table;

class Manage
{
    //  进程监控列表
    private $table;

    //  当前monitor应该传入的配置项
    private $config;

    //进程前缀
    private $pre;

    // 保存 monitor 的pid
    private $monitorPid;

    /*
     *  构造方法，传入参数
     *  要求传入参数中存在
     *  pre：进程名前缀，默认为  tal_
     *  name：子进程的名称，可起名为项目名称
     *  logName：日志文件名称，放于logs/fend目录下
     *  processNum：应该启动的进程数
     *  heartTime：心跳检测最大延迟时间[一般建议10s~30s]
     *  */
    public function __construct($config)
    {
        $this->table = new  Table(4048);
        //  用于存储进程号、进程名称
        $this->table->column('pid', Table::TYPE_INT, 4);
        $this->table->column('pname', Table::TYPE_STRING, 64);
        $this->table->column('timestamp', Table::TYPE_INT, 8);
        $this->table->create();
        $this->config = $config;
        $this->pre = $config['pre'] ?? 'dcr';
    }

    /*
     *  swoole_table  监控主进程
     *  保证所有process进程顺利运行
     *  */
    public function monitorStart()
    {
        pcntl_signal(SIGTERM, [$this, "killMonitor"]);
        declare(ticks=1);
        swoole_set_process_name($this->getProName('master'));
        $this->pid = $this->newMonitor();
        while (1) {
            $ret = Process::wait(false);
            if ($ret) {
                // $ret 是个数组 code是进程退出状态码
                $pid = $ret['pid'];
                LogBase::write("monitor exit, pid=" . $pid . ",restart it!", $this->config['logName']);
                $this->pid = $this->newMonitor();
            }
            sleep(1);
        }
    }

    /**
     * 钩子方法，请编码业务逻辑
     * */
    public function hook()
    {

        // 请于子类中完成之下的业务逻辑处理，或实现一些其他的 dispatch 操作等
    }

    /**
     * 刷新当前进程的心跳时间戳
     * 请于子类业务死循环 [while(true) {...}] 中最开始部分调用其父类中的该方法，否则将无法正确监控
     * */
    protected function flushTimestamp($pid)
    {
        $value = $this->table->get($this->config["pre"] . $pid);
        $value['timestamp'] = time();
        $this->table->set($this->config["pre"] . $pid, $value);
    }

    /**
     * 钩子方法，当进程退出的时候执行一些操作
     */
    protected function exiting()
    {
    }

    /**
     * 启动monitor进程
     * */
    private function newMonitor(): bool|int
    {
        $process = new  Process(function () {
            pcntl_signal(SIGTERM, [$this, "killAllProcess"]);
            pcntl_signal(SIGCHLD, [$this, "childDie"]);
            declare(ticks=1);
            swoole_set_process_name($this->getProName('monitor'));

            while (true) {
                //  检测进程是否需要初始化，其中有一个是monitor的，计数要减一
                $tableCount = count($this->table);
                if ($tableCount < $this->config['processNum']) {
                    LogBase::write('monitor  new  process', $this->config['logName']);
                    for ($i = 0; $i < $this->config['processNum'] - $tableCount; ++$i) {
                        $info = $this->registeProcess($this->config['name']);
                        if (!$info) {
                            LogBase::write('start  work  process  faild,please  check  memory or swoole\'s log', $this->config['logName']);
                        }
                    }
                } else {
                    foreach ($this->table as $index => $value) {
                        if (time() - $value['timestamp'] > $this->config['heartTime']) {
                            Process::kill($value["pid"]);
                            $this->table->del($index);
                        }
                    }
                }
                // 心跳检测
                sleep(1);
            }
        });
        LogBase::write('start monitor process', $this->config['logName']);
        $pid = $process->start();
        if (!$pid) {
            LogBase::write('start  monitor  process  faild,please  check  memory or swoole\'s log', $this->config['logName']);
            return false;
        }
        return $pid;
    }

    /**
     * 注册一个进程
     * desc : 告诉当前 monitor 该进程的pid和pname，实现进程
     * */
    private function registeProcess($name): bool
    {
        $process = new Process(function () use ($name) {
            $pid = getmypid();
            swoole_set_process_name($this->getProName('work_' . $this->pre . $pid));
            pcntl_signal(SIGTERM, [$this, "killMe"]);
            $this->hook();
        });
        $pid = $process->start();
        if ($pid !== false) {
            $key = $this->pre . $pid;
            $this->table->set($key, [
                'pid' => $pid,
                'pname' => $this->getProName('work_' . $key),
                'timestamp' => time(),
            ]);
            return true;
        }

        return false;
    }

    /**
     * 获得进程名称字符串
     * @params  $index
     * */
    private function getProName($suffix)
    {
        return $this->pre . $this->config['name'] . ':' . $suffix;
    }

    /**
     * kill 所有进程，最后在kill自己，并记录日志
     */
    private function killAllProcess()
    {
        LogBase::write('kill all process', $this->config['logName']);
        foreach ($this->table as $index => $item) {
            Process::kill($item['pid']);
        }
        LogBase::write('kill monitor', $this->config['logName']);
        exit(0);
    }

    /**
     * kill monitor
     *
     */
    private function killMonitor()
    {
        LogBase::write('kill monitor process', $this->config['logName']);
        Process::kill($this->monitorPid);
        exit(0);
    }

    /**
     * monitor 接受到子进程发送的死亡信号
     *
     */
    public function childDie()
    {
        while ($ret = Process::wait(false)) {
            // $ret 是个数组 code是进程退出状态码
            $pid = $ret['pid'];
            $this->table->del($this->config["pre"] . $pid);
            LogBase::write('a work process has been gone,wait monitor restart', $this->config['logName']);
        }
    }

    /**
     * 子进程收到 kill 信号
     *
     */
    private function killMe()
    {
        $pid = getmypid();
        $key = ($this->config["pre"] . $pid);
        $this->table->del($key);
        $this->exiting();
        LogBase::write('work process has been killed,pid is ' . $pid, $this->config['logName']);
        exit;
    }

    private function dumpTable()
    {
        $statics = [];
        foreach ($this->table as $k => $v) {
            $statics[$k] = $v;
        }
        LogBase::write(json_encode($statics));
    }
}
