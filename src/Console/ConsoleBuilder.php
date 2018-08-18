<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 9:16 PM
 */


namespace Lvinkim\SwimConsole\Console;


use Lvinkim\SwimConsole\Structure\BuilderParam;
use Lvinkim\SwimConsole\Structure\BuilderResponse;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ConsoleBuilder
{

    /** @var BuilderParam */
    private $builderParam;

    public function __construct(BuilderParam $builderParam)
    {
        $this->builderParam = $builderParam;
    }

    /**
     * @return BuilderResponse
     * @throws \Exception
     */
    public function run()
    {
        $console = $this->buildApplication();

        $console->setAutoExit(false);

        $command = $this->buildCommand();
        $console->add($command);

        $parameters = ['command' => $command->getName()];
        if ($this->builderParam->getCommandOptions()) {
            $parameters = array_merge($parameters, $this->builderParam->getCommandOptions());
        }

        $commandInput = new ArrayInput($parameters);
        $commandOutput = new BufferedOutput();

        $exitCode = $console->run($commandInput, $commandOutput);
        $output = $commandOutput->fetch();

        $builderResponse = new BuilderResponse();
        $builderResponse->setExitCode($exitCode);
        $builderResponse->setOutput($output);

        return $builderResponse;
    }

    /**
     * @return Application
     * @throws \Exception
     */
    private function buildApplication()
    {
        $consoleClassName = $this->builderParam->getConsoleClassName();

        if (!$consoleClassName) {
            $consoleClassName = Application::class;
        }

        if (!class_exists($consoleClassName)) {
            throw new \Exception("{$consoleClassName} not exists");
        }

        $consoleClassParam = $this->builderParam->getConsoleClassParam();

        $console = new $consoleClassName(...$consoleClassParam);

        if (!($console instanceof Application)) {
            throw new \Exception("{$consoleClassName} must extends " . Command::class);
        }

        return $console;
    }

    /**
     * @return Command
     * @throws \Exception
     */
    private function buildCommand()
    {
        $className = $this->builderParam->getClassName();
        if (!class_exists($className)) {
            throw new \Exception("{$className} not exists");
        }

        $classParam = $this->builderParam->getClassParam();
        $command = new $className(...$classParam);

        if (!($command instanceof Command)) {
            throw new \Exception("{$className} must extends " . Command::class);
        }

        return $command;
    }
}