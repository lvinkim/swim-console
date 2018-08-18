<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/6
 * Time: 11:07 PM
 */

namespace Lvinkim\SwimConsole\Daemon;


use Lvinkim\SwimConsole\Console\ConsoleBuilder;
use Lvinkim\SwimConsole\Structure\DaemonJob;

class CommandDaemonWorker implements DaemonWorker
{
    private $daemonJob;

    public function __construct(DaemonJob $daemonJob)
    {
        $this->daemonJob = $daemonJob;
    }

    public function run($index)
    {
        $output = $this->daemonJob->getOutput();

        $outputWriter = function ($message) use ($output) {
            if ($output) {
                if (is_writable($output) || !file_exists($output)) {
                    $datetime = date('Y-m-d H:i:s');
                    file_put_contents($output, "[{$datetime}] " . $message . PHP_EOL, FILE_APPEND);
                }
            }
        };

        try {
            $builderParam = $this->daemonJob->getBuilderParam();
            $consoleBuilder = new ConsoleBuilder($builderParam);

            $builderResponse = $consoleBuilder->run();
            $outputWriter($builderResponse->getOutput());

            $exitCode = $builderResponse->getExitCode();
        } catch (\Throwable $throwable) {
            $outputWriter($throwable->getMessage());
            $exitCode = 0;
        }

        $sleep = $this->daemonJob->isDepends() ? $exitCode : $this->daemonJob->getSleep();

        sleep(intval($sleep));

    }
}