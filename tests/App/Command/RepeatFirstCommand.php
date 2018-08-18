<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/19
 * Time: 12:13 AM
 */

namespace Tests\App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RepeatFirstCommand extends Command
{
    public function configure()
    {
        $this->setName('cmd:repeat:first')
            ->addOption('caller', null, InputOption::VALUE_OPTIONAL, "调用者名称", "none")
            ->setDescription('1号测试脚本');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $caller = $input->getOption('caller');

        $after = rand(3, 6);

        $datetime = date('Y-m-d H:i:s');
        $output->writeln(" [{$datetime}] 1号测试脚本 after-{$after}s - {$caller}");

        file_put_contents(__DIR__ . '/../var/cmd-repeat-first.log', " [{$datetime}] 1号测试脚本 after-{$after}s - {$caller}" . PHP_EOL, FILE_APPEND);

        return $after * 1000;
    }

}