<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 4:10 PM
 */

use Symfony\Component\Console\Application;
use Tests\App\Command\DaemonFirstCommand;
use Tests\App\Command\CrontabSecondCommand;
use Tests\App\Command\CrontabFirstCommand;

require dirname(__DIR__) . '/../../vendor/autoload.php';

$console = new Application('Symfony Console ');

$console->addCommands([
    new CrontabFirstCommand(),
    new CrontabSecondCommand(),
    new DaemonFirstCommand('pass-' . rand(100, 999)),
]);

try {
    $console->run();
} catch (\Exception $exception) {
    die($exception->getMessage() . PHP_EOL);
}
