# swim-console
使用 swoole 对 symfony console 的守护任务，计划定时任务，定时重复任务

> 版本支持  
> php >= 7.1  
> swoole >= 1.7.7  


### 安装
```php
composer require lvinkim/swim-console
```

### 守护任务

```php
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
```


### 计划定时任务

```php
require dirname(__DIR__) . '/../../vendor/autoload.php';

$debug = false;
$period = 60 * 1000;    // 每隔 60*1000ms (1分钟) 触发一次
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

```


### 定时重复任务

```php
require dirname(__DIR__) . '/../../vendor/autoload.php';

$debug = false;
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

    $repeatJobber->run();

} catch (\Error $e) {
    null;
} catch (Exception $e) {
    null;
} finally {
    null;
}
```

### 计划定时系统命令

```php

require dirname(__DIR__) . '/../../vendor/autoload.php';

$debug = false;
$period = 60 * 1000;    // 每隔 60*1000ms (1分钟) 触发一次
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


```