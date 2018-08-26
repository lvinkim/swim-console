<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 5:49 PM
 */

namespace Lvinkim\SwimConsole;


use Lvinkim\SwimConsole\Daemon\CommandDaemonWorker;
use Lvinkim\SwimConsole\Daemon\DaemonProcess;
use Lvinkim\SwimConsole\Structure\BuilderParam;
use Lvinkim\SwimConsole\Structure\DaemonJob;

class DaemonJobber
{
    private $debug = false;

    /** @var DaemonJob[] */
    private $daemonJobs;

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
        $sleep = intval($config["sleep"] ?? 0);
        $depends = boolval($config["depends"] ?? false);
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
        $autoBuildCommand ? $builderParam->setAutoBuildCommand($autoBuildCommand) : null;

        $daemonJob = new DaemonJob($jobId, $builderParam);
        $daemonJob->setEnabled($enabled);
        $daemonJob->setDepends($depends);
        $sleep ? $daemonJob->setSleep($sleep) : null;
        $output ? $daemonJob->setOutput($output) : null;

        $this->daemonJobs[] = $daemonJob;
    }

    public function run()
    {
        $daemonProcess = new DaemonProcess('daemon-jobber-master');

        foreach ($this->daemonJobs as $index => $daemonJob) {

            $jobId = $daemonJob->getJobId();
            $enabled = $daemonJob->isEnabled();

            if (!$enabled) {
                $this->debugLog("job({$jobId}) is not enabled");
                continue;
            }

            if ($this->isDebug()) {
                $daemonWorker = new CommandDaemonWorker($daemonJob);
                $daemonWorker->run($index);
            } else {
                $daemonWorker = new CommandDaemonWorker($daemonJob);
                $daemonProcess->addWorker($jobId, $daemonWorker);
            }

            $this->debugLog("job({$jobId}) added to daemon");
        }

        $daemonProcess->run();
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

    private function debugLog($message)
    {
        if ($this->debugLogFile) {
            if (is_writable($this->debugLogFile) || !file_exists($this->debugLogFile)) {
                $datetime = date('Y-m-d H:i:s');
                file_put_contents($this->debugLogFile, "[{$datetime}] " . $message . PHP_EOL, FILE_APPEND);
            }
        }
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
}