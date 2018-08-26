<?php
/**
 * Created by PhpStorm.
 * User: lvinkim
 * Date: 2018/8/18
 * Time: 6:46 PM
 */

namespace Tests\App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DaemonThirdCommand extends Command
{
    private $pass;

    public function __construct(?string $pass = "")
    {
        parent::__construct();

        $this->pass = strval($pass);
    }

    public function configure()
    {
        $this->setName('cmd:daemon:third')
            ->addOption('caller', null, InputOption::VALUE_OPTIONAL, "调用者名称", "none")
            ->setDescription('3号守护测试脚本');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $caller = $input->getOption('caller');

        mt_srand(microtime(true));
        $sleep = mt_rand(4, 8);

        $unique = uniqid();

        $datetime = date('Y-m-d H:i:s');
        $output->writeln(" [{$datetime}] 3号守护测试脚本 {$unique} sleep-{$sleep}s - {$caller} - {$this->pass}");

        file_put_contents(__DIR__ . '/../var/cmd-daemon-third.log', " [{$datetime}] 3号守护测试脚本 {$unique} sleep-{$sleep}s - {$caller} - {$this->pass}" . PHP_EOL, FILE_APPEND);

        return $sleep;
    }

}