<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 11:54 PM
 */

namespace Lvinkim\SwimConsole\Structure;

class RepeatJob
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
     * 单位 ms 毫秒
     * @var int
     */
    private $interval;

    /**
     * @var bool
     */
    private $depends;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $output;


    public function __construct($jobId, BuilderParam $builderParam, $interval = 1000, $enabled = true)
    {
        $this->jobId = strval($jobId);
        $this->builderParam = $builderParam;
        $this->interval = intval($interval);
        $this->depends = false;
        $this->enabled = $enabled;
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
     * @return int
     */
    public function getInterval(): int
    {
        return $this->interval;
    }

    /**
     * @param int $interval
     */
    public function setInterval(int $interval): void
    {
        $this->interval = $interval;
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

    /**
     * @return bool
     */
    public function isDepends(): bool
    {
        return $this->depends;
    }

    /**
     * @param bool $depends
     */
    public function setDepends(bool $depends): void
    {
        $this->depends = $depends;
    }


}