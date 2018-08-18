<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/6
 * Time: 10:16 PM
 */

namespace Lvinkim\SwimConsole\Daemon;

interface DaemonWorker
{
    public function run($index);
}