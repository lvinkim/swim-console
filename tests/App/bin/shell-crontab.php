<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/19
 * Time: 11:43 AM
 */

require dirname(__DIR__) . '/../../vendor/autoload.php';

$debug = false;
$period = 15 * 1000;    // 每隔 60*1000ms (1分钟) 触发一次
$mission = function ($timerId) use ($debug) {

    $console = __DIR__ . '/console.php';
    $logDir = __DIR__ . '/../var';
    $date = date('Y-m-d');

    try {

        $shellJobber = new \Lvinkim\SwimConsole\ShellJobber($debug);
        $shellJobber->setDebugLogFile($logDir . "/shell-crontab-job-debug.log." . $date);

        $shellJobber->add("cmd-first", [
            "command" => "/usr/bin/env php {$console} cmd:shell-crontab:first --caller=shell-crontab",
            "schedule" => "* * * * *",
            "enabled" => true,
            "output" => $logDir . "/shell-crontab-cmd-first.log." . $date,
        ]);

        $shellJobber->add("cmd-second", [
            "command" => "/usr/bin/env php {$console} cmd:shell-crontab:second --caller=shell-crontab",
            "schedule" => "* * * * *",
            "enabled" => true,
            "output" => $logDir . "/shell-crontab-cmd-second.log." . $date,
        ]);

        $shellJobber->run();

    } catch (\Throwable $e) {
        null;
    }
};

if ($debug) {
    $mission(uniqid());
} else {
    $timer = swoole_timer_tick($period, $mission);
}
