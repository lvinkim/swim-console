<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 8:52 PM
 */

namespace Lvinkim\SwimConsole\Structure;


class CrontabJob
{

    /**
     * @var string
     */
    private $jobId;

    /**
     * @var BuilderParam
     */
    private $builderParam;

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

    public function __construct($jobId, BuilderParam $builderParam, $schedule = "* * * * *", $enabled = true)
    {
        $this->jobId = strval($jobId);
        $this->builderParam = $builderParam;
        $this->schedule = strval($schedule);
        $this->enabled = $enabled;
        $this->output = "";
    }

    /**
     * @return string
     */
    public function getJobId(): string
    {
        return strval($this->jobId);
    }

    /**
     * @param string $jobId
     */
    public function setJobId(string $jobId): void
    {
        $this->jobId = $jobId;
    }

    /**
     * @return BuilderParam
     */
    public function getBuilderParam(): BuilderParam
    {
        return $this->builderParam;
    }

    /**
     * @param BuilderParam $builderParam
     */
    public function setBuilderParam(BuilderParam $builderParam): void
    {
        $this->builderParam = $builderParam;
    }

    /**
     * @return string
     */
    public function getSchedule(): string
    {
        return strval($this->schedule);
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
        return boolval($this->enabled);
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
        return strval($this->output);
    }

    /**
     * @param string $output
     */
    public function setOutput(string $output): void
    {
        $this->output = $output;
    }


}