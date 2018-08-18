<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 10:01 PM
 */

namespace Tests\Functional\ConsoleBuilder;


use Lvinkim\SwimConsole\Console\ConsoleBuilder;
use Lvinkim\SwimConsole\Structure\BuilderParam;
use PHPUnit\Framework\TestCase;
use Tests\App\Command\DaemonFirstCommand;

class ConsoleBuilderTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testRun()
    {
        $builderParam = new BuilderParam(
            DaemonFirstCommand::class,
            ['pass-' . rand(100, 999)],
            ['--caller' => 'unit-test']
        );

        $consoleBuilder = new ConsoleBuilder($builderParam);
        $builderResponse = $consoleBuilder->run();

        $this->assertEquals(0, $builderResponse->getExitCode());
    }
}