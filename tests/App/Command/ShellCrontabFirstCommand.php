<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/19
 * Time: 12:02 PM
 */

namespace Tests\App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShellCrontabFirstCommand extends Command
{

    public function configure()
    {
        $this->setName('cmd:shell-crontab:first')
            ->addOption('caller', null, InputOption::VALUE_OPTIONAL, "调用者名称", "none")
            ->setDescription('1号测试脚本');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $caller = $input->getOption('caller');

        mt_srand(microtime(true));
        $sleep = mt_rand(3, 6);

        sleep($sleep);

        $datetime = date('Y-m-d H:i:s');
        $message = " [{$datetime}] 1号测试脚本 sleep {$sleep}s - {$caller}";
        $output->writeln($message);

        file_put_contents(__DIR__ . '/../var/cmd-shell-crontab-first.log', $message . PHP_EOL, FILE_APPEND);
    }

}