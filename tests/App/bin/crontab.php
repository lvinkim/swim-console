<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 4:11 PM
 */

require dirname(__DIR__) . '/../../vendor/autoload.php';

$debug = false;
$period = 15 * 1000;    // 每隔 60*1000ms (1分钟) 触发一次
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
    $timer = swoole_timer_tick($period, $mission);
}


