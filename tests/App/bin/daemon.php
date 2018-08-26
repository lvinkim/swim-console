<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 4:11 PM
 */


require_once dirname(__DIR__) . '/../../vendor/autoload.php';

$debug = true;
$logDir = __DIR__ . '/../var';
$date = date('Y-m-d');

try {

    $daemonJobber = new \Lvinkim\SwimConsole\DaemonJobber($debug);
    $daemonJobber->setDebugLogFile($logDir . "/daemon-job-debug.log." . $date);

    $daemonJobber->add('cmd-first', [
        'command' => \Tests\App\Command\DaemonFirstCommand::class,
        "commandParam" => ["pass-" . rand(100, 999)],
        'sleep' => 2,   // s
        'enabled' => true,
        "output" => $logDir . "/daemon-cmd-first.log." . $date,
    ]);

    $daemonJobber->add('cmd-second', [
        'command' => \Tests\App\Command\DaemonSecondCommand::class,
        'commandOptions' => ["--caller" => "daemon"],
        'depends' => true,
        'enabled' => true,
        "output" => $logDir . "/daemon-cmd-second.log." . $date,
    ]);

    $daemonJobber->add("cmd-third", [
        "console" => Symfony\Component\Console\Application::class,
        "command" => \Tests\App\Command\DaemonThirdCommand::class,
        "commandName" => "cmd:daemon:third",
        'commandOptions' => ["--caller" => "daemon"],
        'depends' => true,
        'enabled' => true,
        "output" => $logDir . "/daemon-cmd-third.log." . $date,
    ]);

    $daemonJobber->run();

} catch (\Throwable $e) {
    null;
}