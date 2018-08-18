<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 4:11 PM
 */

require_once dirname(__DIR__) . '/../../vendor/autoload.php';

$debug = false;
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

    $daemonJobber->run();

} catch (\Throwable $e) {
    null;
}