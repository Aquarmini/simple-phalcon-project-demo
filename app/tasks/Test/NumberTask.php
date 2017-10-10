<?php

namespace App\Tasks\Test;

use App\Tasks\Task;
use Xin\Cli\Color;
use SebastianBergmann\CodeCoverage\Report\PHP;

class NumberTask extends Task
{

    public function mainAction()
    {
        echo Color::head('Help:'), PHP_EOL;
        echo Color::colorize('  测试脚本列表'), PHP_EOL, PHP_EOL;

        echo Color::head('Usage:'), PHP_EOL;
        echo Color::colorize('  php run Test:number@[action]', Color::FG_GREEN), PHP_EOL, PHP_EOL;
        echo Color::head('Actions:'), PHP_EOL;
        echo Color::colorize('  format                     number_format测试', Color::FG_GREEN), PHP_EOL;
        echo Color::colorize('  decbin                     decbin 10进制到2进制 测试', Color::FG_GREEN), PHP_EOL;
        echo Color::colorize('  bc                         高精度运算', Color::FG_GREEN), PHP_EOL;
    }

    public function bcAction()
    {
        $a = 0.1;
        $b = 0.7;
        $scale = 2; // 精确到小数点位数
        echo Color::colorize("普通加法：") . PHP_EOL;
        dump($a + $b == 0.8);
        echo Color::colorize("高精度加法：") . PHP_EOL;
        dump(bcadd($a, $b, $scale) == 0.8);

    }

    public function decbinAction()
    {
        $time = time() - strtotime('2017-01-01');
        // $time = strtotime('2018-01-01') - strtotime('2017-01-01');

        // 64bit
        $bit = decbin($time);
        $bit = str_pad($bit, 42, '0', STR_PAD_LEFT);
        $int = str_pad($bit, 64, '0');
        dd(bindec($int));
    }

    public function formatAction()
    {
        echo Color::colorize("保留2位小数:", Color::FG_RED) . PHP_EOL;
        echo Color::colorize("执行：number_format('1.222222', 2)") . PHP_EOL;
        echo Color::colorize("结果:" . number_format('1.222222', 2), Color::FG_GREEN) . PHP_EOL;

        echo Color::colorize("执行：number_format(99.222222, 2)") . PHP_EOL;
        echo Color::colorize("结果:" . number_format(99.222222, 2), Color::FG_GREEN) . PHP_EOL;
    }

}

