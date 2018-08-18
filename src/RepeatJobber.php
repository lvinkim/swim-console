<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 6:58 PM
 */

namespace Lvinkim\SwimConsole;


use Lvinkim\SwimConsole\Console\ConsoleBuilder;
use Lvinkim\SwimConsole\Structure\BuilderParam;
use Lvinkim\SwimConsole\Structure\RepeatJob;

class RepeatJobber
{
    private $debug = false;

    /** @var RepeatJob[] */
    private $repeatJobs;

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
        $className = strval($config["command"] ?? "");
        $interval = intval($config["interval"] ?? 0);
        $depends = boolval($config["depends"] ?? false);
        $enabled = boolval($config["enabled"] ?? false);
        $output = strval($config["output"] ?? "");
        $classParam = (array)($config["commandParam"] ?? []);
        $commandOptions = (array)($config["commandOptions"] ?? []);
        $consoleClassName = strval($config["console"] ?? "");
        $consoleClassParam = (array)($config["consoleParam"] ?? []);

        $builderParam = new BuilderParam($className);
        $classParam ? $builderParam->setClassParam($classParam) : null;
        $commandOptions ? $builderParam->setCommandOptions($commandOptions) : null;
        $consoleClassName ? $builderParam->setConsoleClassName($consoleClassName) : null;
        $consoleClassParam ? $builderParam->setConsoleClassParam($consoleClassParam) : null;

        $repeatJob = new RepeatJob($jobId, $builderParam);
        $repeatJob->setEnabled($enabled);
        $repeatJob->setDepends($depends);
        $interval ? $repeatJob->setInterval($interval) : null;
        $output ? $repeatJob->setOutput($output) : null;

        $this->repeatJobs[] = $repeatJob;
    }

    public function run()
    {
        foreach ($this->repeatJobs as $repeatJob) {

            $jobId = $repeatJob->getJobId();
            $enabled = $repeatJob->isEnabled();

            if (!$enabled) {
                $this->debugLog("job({$jobId}) is not enabled");
                continue;
            }

            $isDebug = $this->isDebug();

            $mission = function (RepeatJob $repeatJob) use (&$mission, $isDebug) {

                $output = $repeatJob->getOutput();

                $outputWriter = function ($message) use ($output) {
                    if ($output) {
                        if (is_writable($output) || !file_exists($output)) {
                            $datetime = date('Y-m-d H:i:s');
                            file_put_contents($output, "[{$datetime}] " . $message . PHP_EOL, FILE_APPEND);
                        }
                    }
                };

                try {
                    $builderParam = $repeatJob->getBuilderParam();
                    $consoleBuilder = new ConsoleBuilder($builderParam);

                    $builderResponse = $consoleBuilder->run();
                    $outputWriter($builderResponse->getOutput());

                    $exitCode = $builderResponse->getExitCode();
                } catch (\Throwable $throwable) {
                    $outputWriter($throwable->getMessage());
                    $exitCode = 0;
                }

                if (!$isDebug) {
                    if ($repeatJob->isDepends()) {
                        $interval = $exitCode > 0 ? $exitCode : 1000;
                    } else {
                        $interval = $repeatJob->getInterval();
                    }
                    swoole_timer_after($interval, $mission, $repeatJob);
                }
            };

            $this->debugLog("job({$jobId}) start with {$repeatJob->getInterval()} ms - (depends:{$repeatJob->isDepends()})");

            if (!$isDebug) {
                $interval = $repeatJob->isDepends() ? 1000 : $repeatJob->getInterval();
                swoole_timer_after($interval, $mission, $repeatJob);
            } else {
                $mission($repeatJob);
            }
        }
    }

    /**
     * @return string
     */
    public function getDebugLogFile()
    {
        return $this->debugLogFile;
    }

    /**
     * @param string $debugLogFile
     */
    public function setDebugLogFile(string $debugLogFile): void
    {
        $this->debugLogFile = $debugLogFile;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
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