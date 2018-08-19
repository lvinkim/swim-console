<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/19
 * Time: 11:51 AM
 */

namespace Lvinkim\SwimConsole\Structure;

class ShellJob
{
    /**
     * @var string
     */
    private $jobId;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $schedule;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $output;

    public function __construct($jobId, $command, $schedule = "* * * * *", $enabled = true)
    {
        $this->jobId = strval($jobId);
        $this->command = strval($command);
        $this->schedule = strval($schedule);
        $this->enabled = boolval($enabled);
        $this->output = "";
    }

    /**
     * @return string
     */
    public function getJobId(): string
    {
        return $this->jobId;
    }

    /**
     * @param string $jobId
     */
    public function setJobId(string $jobId): void
    {
        $this->jobId = $jobId;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @param string $command
     */
    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getSchedule(): string
    {
        return $this->schedule;
    }

    /**
     * @param string $schedule
     */
    public function setSchedule(string $schedule): void
    {
        $this->schedule = $schedule;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @param string $output
     */
    public function setOutput(string $output): void
    {
        $this->output = $output;
    }


}