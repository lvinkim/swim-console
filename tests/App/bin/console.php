<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 4:10 PM
 */

use Symfony\Component\Console\Application;
use Tests\App\Command\CrontabThirdCommand;
use Tests\App\Command\DaemonFirstCommand;
use Tests\App\Command\CrontabSecondCommand;
use Tests\App\Command\CrontabFirstCommand;
use Tests\App\Command\DaemonThirdCommand;
use Tests\App\Command\RepeatThirdCommand;
use Tests\App\Command\ShellCrontabFirstCommand;
use Tests\App\Command\ShellCrontabSecondCommand;

require dirname(__DIR__) . '/../../vendor/autoload.php';

$console = new Application('Symfony Console ');

$console->addCommands([
    new CrontabFirstCommand(),
    new CrontabSecondCommand(),
    new CrontabThirdCommand(),
    new DaemonFirstCommand('pass-' . rand(100, 999)),
    new DaemonThirdCommand(),
    new ShellCrontabFirstCommand(),
    new ShellCrontabSecondCommand(),
    new RepeatThirdCommand(),
]);

try {
    $console->run();
} catch (\Exception $exception) {
    die($exception->getMessage() . PHP_EOL);
}
