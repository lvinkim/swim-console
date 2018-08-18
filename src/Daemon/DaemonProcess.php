<?php
/**
 * Created by PhpStorm.
 * User: vinkim
 * Date: 5/17/18
 * Time: 5:23 PM
 */

namespace Lvinkim\SwimConsole\Daemon;

use Swoole\Process;

/**
 * 主进程守护
 * Class DaemonProcess
 * @package App\Swoole
 */
class DaemonProcess
{
    /**
     * 进程名
     * @var string
     */
    private $processName;

    /**
     * 主进程 ID
     * @var int
     */
    private $masterPid = 0;

    /**
     * 所有子进程
     * @var array
     */
    private $works = [];

    /**
     * @var array
     */
    private $pids = [];

    /** @var array */
    private $workerIds = [];

    public function __construct($processName)
    {
        $this->processName = $processName;
    }

    public function addWorker(string $id, DaemonWorker $daemonWorker)
    {
        $this->works[$id] = $daemonWorker;
    }

    public function run()
    {
        if (!$this->works) {
            die("no worker");
        }

        try {
            swoole_set_process_name("{$this->processName}:master"); // 设置主进程的进程名
            $this->masterPid = posix_getpid();  // 记录主进程 ID

            // 创建子进程
            $index = 0;
            foreach ($this->works as $id => $daemonWorker) {
                $index++;
                $this->createProcess($index, $id);
            }

            $this->processWait();   // 主进程等待子进程结束
        } catch (\Exception $e) {
            die('ALL ERROR: ' . $e->getMessage());
        }
    }

    private function createProcess($index, $id)
    {
        $process = new Process(function (Process $worker) use ($index, $id) {

            swoole_set_process_name("{$this->processName}:worker-{$id}");

            try {

                /** @var DaemonWorker $daemonWorker */
                $daemonWorker = $this->works[$id];
                $daemonWorker->run($index); // 运行子进程逻辑

            } catch (\Throwable $exception) {
                echo $exception->getMessage() . PHP_EOL;
            } finally {
                // 检查主进程是否还在，如果主进程已经不在，子进程也要跟着退出
                if (!Process::kill($this->masterPid, 0)) {
                    $worker->exit(1);
                }
            }

        }, false, false);

        $pid = $process->start();

        $this->pids[$index] = $pid;
        $this->workerIds[$index] = $id;

        return $pid;
    }

    /**
     * @throws \Exception
     */
    private function processWait()
    {
        while (1) {
            if (count($this->pids)) {
                $ret = Process::wait();
                if ($ret) {
                    $this->rebootProcess($ret);
                }
            } else {
                break;
            }
        }
    }

    /**
     * @param $ret
     * @throws \Exception
     */
    private function rebootProcess($ret)
    {
        $pid = $ret['pid'];
        $index = array_search($pid, $this->pids);

        if ($index !== false) {
            $index = intval($index);
            $workerId = $this->workerIds[$index];
            $new_pid = $this->createProcess($index, $workerId);
            return;
        }

        throw new \Exception('rebootProcess Error: no pid');
    }

}