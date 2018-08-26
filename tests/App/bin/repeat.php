<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 4:12 PM
 */


require dirname(__DIR__) . '/../../vendor/autoload.php';

$debug = true;
$logDir = __DIR__ . '/../var';
$date = date('Y-m-d');

try {

    $repeatJobber = new Lvinkim\SwimConsole\RepeatJobber($debug);
    $repeatJobber->setDebugLogFile($logDir . "/repeat-job-debug.log." . $date);

    $repeatJobber->add('cmd-first', [
        'command' => \Tests\App\Command\RepeatFirstCommand::class,
        'depends' => true,
        'enabled' => true,
        "output" => $logDir . "/repeat-cmd-first.log." . $date,
    ]);

    $repeatJobber->add('cmd-second', [
        'command' => \Tests\App\Command\RepeatSecondCommand::class,
        'interval' => 8000, // ms
        'enabled' => true,
        "output" => $logDir . "/repeat-cmd-second.log." . $date,
    ]);

    $repeatJobber->add("cmd-third", [
        "console" => Symfony\Component\Console\Application::class,
        "command" => \Tests\App\Command\RepeatThirdCommand::class,
        "commandName" => "cmd:repeat:third",
        'interval' => 3000, // ms
        'enabled' => true,
        "output" => $logDir . "/repeat-cmd-third.log." . $date,
    ]);

    $repeatJobber->run();

} catch (\Error $e) {
    null;
} catch (Exception $e) {
    null;
} finally {
    null;
}