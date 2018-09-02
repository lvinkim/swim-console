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

// 使当前进程蜕变为守护进程
\Swoole\Process::daemon();
// 创建子进程
$process = new \Swoole\Process('callback_function', true);
//执行系统调用
$pid = $process->start();
// 子进程执行的逻辑
function callback_function(\Swoole\Process $worker)
{
    swoole_timer_tick(1000,function(){
        echo time()."/n";
    });
}
// 父进程执行的逻辑
\Swoole\Process::wait();

// 以上代码是一个简单的多进程脚本