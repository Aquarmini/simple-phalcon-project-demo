<?php
// +----------------------------------------------------------------------
// | 测试脚本 [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Tasks\Test;

use App\Utils\Log;
use Phalcon\Cli\Task;
use Xin\Cli\Color;

class MatchTask extends Task
{
    public function mainAction()
    {
        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  测试脚本列表') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run Test\\\\Match [Action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;
        echo Color::head('Action:') . PHP_EOL;
        echo Color::colorize('  match                       正则表达式测试', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  replaceCallback             正则替换Callback方法', Color::FG_GREEN) . PHP_EOL;
    }

    public function replaceCallbackAction()
    {
        $string = 'SELECT * FROM `users` WHERE id IN (?,?,?,?,?,?,?,?)';
        $bindings = [1, 2, 3, 4, 5, 6, 7, 8];
        $len = 10000;

        $time = microtime(true);
        echo $time . PHP_EOL;
        echo microtime(true) . PHP_EOL;

        for ($i = 0; $i < $len; $i++) {
            Log::info($string);
        }
        echo Color::colorize('耗时：' . (microtime(true) - $time)) . PHP_EOL;

        $time = microtime(true);
        for ($i = 0; $i < $len; $i++) {
            $bindings = [1, 2, 3, 4, 5, 6, 7, 8];
            $sql = preg_replace_callback('~\?~', function ($matches) use (&$bindings) {
                    try {
                        return "'" . array_shift($bindings) . "'";
                    } catch (\Exception $ex) {

                    }
                }, $string) . ';';
            Log::info($sql);
        }
        echo Color::colorize('耗时：' . (microtime(true) - $time)) . PHP_EOL;
    }

    public function matchAction()
    {
        $vals = ["qianrong.gold30", "qianrong.coin30"];
        echo Color::head("正则表达式：") . PHP_EOL;
        foreach ($vals as $val) {
            echo Color::colorize("  " . $val) . PHP_EOL;
            preg_match("/(coin|gold)[0-9]+$/", $val, $res);
            echo Color::colorize("  " . json_encode($res)) . PHP_EOL;
        }
    }

}