<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/19
 * Time: 12:35 AM
 */

namespace Tests\App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RepeatThirdCommand extends Command
{
    public function configure()
    {
        $this->setName('cmd:repeat:third')
            ->addOption('caller', null, InputOption::VALUE_OPTIONAL, "调用者名称", "none")
            ->setDescription('3号测试脚本');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $caller = $input->getOption('caller');

        $after = rand(6, 9);

        $datetime = date('Y-m-d H:i:s');
        $output->writeln(" [{$datetime}] 3号测试脚本 after-{$after}s - {$caller}");

        file_put_contents(__DIR__ . '/../var/cmd-repeat-third.log', " [{$datetime}] 3号测试脚本 after-{$after}s - {$caller}" . PHP_EOL, FILE_APPEND);

        return $after * 1000;
    }

}
