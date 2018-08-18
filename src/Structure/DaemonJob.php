<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/19
 * Time: 12:51 AM
 */

namespace Lvinkim\SwimConsole\Structure;


class DaemonJob
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
     * 单位 s 秒
     * @var int
     */
    private $sleep;

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

    public function __construct($jobId, BuilderParam $builderParam, $sleep = 1, $enabled = true)
    {
        $this->jobId = strval($jobId);
        $this->builderParam = $builderParam;
        $this->sleep = intval($sleep);
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
    public function getSleep(): int
    {
        return $this->sleep;
    }

    /**
     * @param int $sleep
     */
    public function setSleep(int $sleep): void
    {
        $this->sleep = $sleep;
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