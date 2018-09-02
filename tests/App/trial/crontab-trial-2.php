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


$timer = swoole_timer_tick(1 * 1000, function () {

    $process = new \Swoole\Process(function () {

        \Swoole\Process::daemon();
        swoole_set_process_name("timer1-process");

        echo "timer 1 : " . date("Y-m-d H:i:s") . PHP_EOL;

        sleep(3);

    });
    $process->start();

    \Swoole\Process::wait();

});

