<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/19
 * Time: 11:49 AM
 */

namespace Lvinkim\SwimConsole;


use Jobby\ScheduleChecker;
use Lvinkim\SwimConsole\Structure\ShellJob;
use Swoole\Process;

class ShellJobber
{
    private $debug = false;

    /** @var ShellJob[] */
    private $shellJobs = [];

    /** @var string */
    private $debugLogFile;

    public function __construct($debug = false)
    {
        $this->debug = boolval($debug);
    }

    /**
     * @param $jobId
     * @param array $config
     */
    public function add($jobId, array $config)
    {
        $command = strval($config["command"] ?? "");
        $schedule = strval($config["schedule"] ?? "");
        $enabled = boolval($config["enabled"] ?? false);
        $output = strval($config["output"] ?? "");

        $shellJob = new ShellJob($jobId, $command);
        $shellJob->setEnabled($enabled);
        $schedule ? $shellJob->setSchedule($schedule) : null;
        $output ? $shellJob->setOutput($output) : null;

        $this->shellJobs[] = $shellJob;
    }

    public function run()
    {
        $scheduleChecker = new ScheduleChecker();

        foreach ($this->shellJobs as $shellJob) {

            $jobId = $shellJob->getJobId();
            $enabled = $shellJob->isEnabled();
            $schedule = $shellJob->getSchedule();
            $command = $shellJob->getCommand();

            if (!$enabled) {
                $this->debugLog("job({$jobId}) is not enabled");
                continue;
            }

            if (!$scheduleChecker->isDue($schedule)) {
                $this->debugLog("job({$jobId}) is not schedule with ({$schedule})");
                continue;
            }

            if (!$command) {
                $this->debugLog("job({$jobId}) no command setting");
                continue;
            }

            $childProcess = function (Process $commandProcess) use ($shellJob) {

                Process::daemon();  // 将子进程蜕变为守护进程

                $command = $shellJob->getCommand();
                $output = $shellJob->getOutput();

                $output = $output ? $output : "/dev/null";
                $command = "{$command} 1> $output 2>&1 &";

                $commandProcess->exec('/bin/sh', ['-c', $command]);

            };

            $this->debugLog("job({$jobId}) start with ({$schedule})");

            if ($this->isDebug()) {
                null;
            } else {
                $process = new Process($childProcess, false, false);
                $process->start();
            }

        }

        if (!$this->isDebug()) {
            while (1) {
                $ret = Process::wait(); // 必须等待所有子进程退出并回收资源，否则会产生僵尸进程
                if (!$ret) {
                    break;
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getDebugLogFile()
    {
        return $this->debugLogFile;
    }

    /**
     * @param mixed $debugLogFile
     */
    public function setDebugLogFile($debugLogFile): void
    {
        $this->debugLogFile = $debugLogFile;
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    private function debugLog($message)
    {
        if ($this->debugLogFile) {
            if (is_writable($this->debugLogFile) || !file_exists($this->debugLogFile)) {
                $datetime = date('Y-m-d H:i:s');
                file_put_contents($this->debugLogFile, "[{$datetime}] " . $message . PHP_EOL, FILE_APPEND);
            }
        }
    }
}