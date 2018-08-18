<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 9:21 PM
 */

namespace Lvinkim\SwimConsole\Structure;


class BuilderParam
{
    /**
     * Command 的类名
     * @var string
     */
    private $className;

    /**
     * Command 构造函数所需参数
     * @var array
     */
    private $classParam;

    /**
     * 执行 Command 时传入的参数
     * @var array
     */
    private $commandOptions;

    /**
     * 自定义的 Application 类名
     * @var string
     */
    private $consoleClassName;

    /**
     * Application 构造函数所需参数
     * @var array
     */
    private $consoleClassParam;

    public function __construct($className, array $classParam = [], array $commandOptions = [])
    {
        $this->className = strval($className);
        $this->classParam = $classParam;
        $this->commandOptions = $commandOptions;

        $this->consoleClassName = "";
        $this->consoleClassParam = [];
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param mixed $className
     */
    public function setClassName($className): void
    {
        $this->className = $className;
    }

    /**
     * @return mixed
     */
    public function getClassParam()
    {
        return $this->classParam;
    }

    /**
     * @param mixed $classParam
     */
    public function setClassParam($classParam): void
    {
        $this->classParam = $classParam;
    }

    /**
     * @return mixed
     */
    public function getCommandOptions()
    {
        return $this->commandOptions;
    }

    /**
     * @param mixed $commandOptions
     */
    public function setCommandOptions($commandOptions): void
    {
        $this->commandOptions = $commandOptions;
    }

    /**
     * @return string
     */
    public function getConsoleClassName(): string
    {
        return $this->consoleClassName;
    }

    /**
     * @param string $consoleClassName
     */
    public function setConsoleClassName(string $consoleClassName): void
    {
        $this->consoleClassName = $consoleClassName;
    }

    /**
     * @return array
     */
    public function getConsoleClassParam(): array
    {
        return $this->consoleClassParam;
    }

    /**
     * @param array $consoleClassParam
     */
    public function setConsoleClassParam(array $consoleClassParam): void
    {
        $this->consoleClassParam = $consoleClassParam;
    }


}