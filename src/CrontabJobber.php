<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 4:32 PM
 */

namespace Lvinkim\SwimConsole;

use Jobby\ScheduleChecker;
use Lvinkim\SwimConsole\Console\ConsoleBuilder;
use Lvinkim\SwimConsole\Structure\BuilderParam;
use Lvinkim\SwimConsole\Structure\CrontabJob;
use Swoole\Process;

class CrontabJobber
{
    private $debug = false;

    /** @var CrontabJob[] */
    private $crontabJobs = [];

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
        $commandName = strval($config["commandName"] ?? "");
        $className = strval($config["command"] ?? "");
        $schedule = strval($config["schedule"] ?? "");
        $enabled = boolval($config["enabled"] ?? false);
        $output = strval($config["output"] ?? "");
        $classParam = (array)($config["commandParam"] ?? []);
        $commandOptions = (array)($config["commandOptions"] ?? []);
        $consoleClassName = strval($config["console"] ?? "");
        $consoleClassParam = (array)($config["consoleParam"] ?? []);
        $autoBuildCommand = boolval($config["autoBuildCommand"] ?? true);

        $builderParam = new BuilderParam($className);
        $commandName ? $builderParam->setCommandName($commandName) : null;
        $classParam ? $builderParam->setClassParam($classParam) : null;
        $commandOptions ? $builderParam->setCommandOptions($commandOptions) : null;
        $consoleClassName ? $builderParam->setConsoleClassName($consoleClassName) : null;
        $consoleClassParam ? $builderParam->setConsoleClassParam($consoleClassParam) : null;
        $builderParam->setAutoBuildCommand($autoBuildCommand);

        $crontabJob = new CrontabJob($jobId, $builderParam);
        $crontabJob->setEnabled($enabled);
        $schedule ? $crontabJob->setSchedule($schedule) : null;
        $output ? $crontabJob->setOutput($output) : null;

        $this->crontabJobs[] = $crontabJob;
    }

    public function run()
    {
        $scheduleChecker = new ScheduleChecker();
        foreach ($this->crontabJobs as $crontabJob) {

            $jobId = $crontabJob->getJobId();
            $enabled = $crontabJob->isEnabled();
            $schedule = $crontabJob->getSchedule();

            if (!$enabled) {
                $this->debugLog("job({$jobId}) is not enabled");
                continue;
            }

            if (!$scheduleChecker->isDue($schedule)) {
                $this->debugLog("job({$jobId}) is not schedule with ({$schedule})");
                continue;
            }

            $childProcess = function () use ($crontabJob) {

                Process::daemon();  // 将子进程蜕变为守护进程

                swoole_set_process_name("job-" . $crontabJob->getJobId());

                $output = $crontabJob->getOutput();

                $outputWriter = function ($message) use ($output) {
                    if ($output) {
                        if (is_writable($output) || !file_exists($output)) {
                            $datetime = date('Y-m-d H:i:s');
                            file_put_contents($output, "[{$datetime}] " . $message . PHP_EOL, FILE_APPEND);
                        }
                    }
                };

                try {
                    $builderParam = $crontabJob->getBuilderParam();
                    $consoleBuilder = new ConsoleBuilder($builderParam);

                    $builderResponse = $consoleBuilder->run();
                    $outputWriter($builderResponse->getOutput());
                } catch (\Throwable $throwable) {
                    $outputWriter($throwable->getMessage());
                }
            };

            $this->debugLog("job({$jobId}) start with ({$schedule})");

            if ($this->isDebug()) {
                $childProcess();
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