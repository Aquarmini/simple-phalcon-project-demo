<?php
// +----------------------------------------------------------------------
// | 测试脚本 [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Tasks\Test;

use App\Core\System;
use App\Test\Obj\StaticFuncClass;
use Phalcon\Cli\Task;
use Xin\Cli\Color;

class PhpTask extends Task
{
    public function mainAction()
    {
        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  PHP函数测试') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run test:php@[action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Actions:') . PHP_EOL;
        echo Color::colorize('  static      静态非静态函数调用测试', Color::FG_GREEN) . PHP_EOL;
    }

    public function staticAction()
    {
        foreach (System::getInstance()->getEnvironment() as $key => $val) {
            echo Color::colorize($key . ':' . $val, Color::FG_GREEN) . PHP_EOL;
        }
        echo PHP_EOL;

        $str = StaticFuncClass::test2();
        echo Color::colorize('静态调用静态方法：' . $str, Color::FG_GREEN) . PHP_EOL;

        try {
            $str = StaticFuncClass::test1();
        } catch (\Exception $ex) {
            echo Color::colorize('静态调用非静态方法：' . $ex->getMessage(), Color::FG_RED) . PHP_EOL;
        }

        $str = StaticFuncClass::getInstance()->test1();
        echo Color::colorize('非静态调用非静态方法：' . $str, Color::FG_GREEN) . PHP_EOL;

        $str = StaticFuncClass::getInstance()->test2();
        echo Color::colorize('非静态调用静态方法：' . $str, Color::FG_GREEN) . PHP_EOL;
    }
}