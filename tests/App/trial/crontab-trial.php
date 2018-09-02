<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/9/2
 * Time: 4:39 PM
 */

/**
 * 每 10 秒钟执行一次 N 个进程，如果其中某些进程执行时间超过 10 秒钟会怎么样？
 */

require dirname(__DIR__) . '/../../vendor/autoload.php';

$debug = false;
$period = 10 * 1000;    // 每隔 60*1000ms (1分钟) 触发一次
$mission = function ($timerId) use ($debug) {

    $logDir = __DIR__ . '/../var';
    $date = date('Y-m-d');

    try {

        $crontabJobber = new \Lvinkim\SwimConsole\CrontabJobber($debug);
        $crontabJobber->setDebugLogFile($logDir . "/crontab-job-debug.log." . $date);

        $crontabJobber->add("cmd-first", [
            "command" => \Tests\App\Command\CrontabFirstCommand::class,
            "schedule" => "* * * * *",
            "enabled" => true,
            "output" => $logDir . "/crontab-cmd-first.log." . $date,
        ]);

        $crontabJobber->add('cmd-second', [
            "command" => \Tests\App\Command\CrontabSecondCommand::class,
            "commandOptions" => ["--caller" => "crontab"],
            "schedule" => '* * * * *',
            "enabled" => true,
            "output" => $logDir . "/crontab-cmd-second.log." . $date,
        ]);

        $crontabJobber->run();

    } catch (\Throwable $e) {
        null;
    }
};

if ($debug) {
    $mission(uniqid());
} else {
    \Swoole\Process::daemon();
    $timer = swoole_timer_tick($period, $mission);
}

