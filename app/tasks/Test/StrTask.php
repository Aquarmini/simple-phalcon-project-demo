<?php
// +----------------------------------------------------------------------
// | 测试脚本 [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Tasks\Test;

use Illuminate\Support\Str;
use Phalcon\Cli\Task;
use Xin\Cli\Color;
use Phalcon\Text;

class StrTask extends Task
{
    public function mainAction()
    {
        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  PHP函数参数测试') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run Test\\\\Str [action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Actions:') . PHP_EOL;
        echo Color::colorize('  random      {$1}        随机字符串', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  strPad                  不足位数补0', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  strstr                  检测字符串是否包含另外一个字符串', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  textRandom              Phalcon\\Text随机字符串', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  camelize                大驼峰转化', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  match                   检测字符串是否存在中文', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  pregReplace             通过正则替换字符串', Color::FG_GREEN) . PHP_EOL;
    }

    public function pregReplaceAction()
    {
        $name = 'https://xxx.sss.jpg#click';
        $match = "/\.jpg|\.png/";
        preg_match($match, $name, $result);
        if (isset($result[0])) {
            $ext = $result[0];
            $res = str_replace($ext, '_xx' . $ext, $name);
        }
        echo Color::colorize('初始字符串:' . $name, Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('匹配正则:' . $match, Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('替换结果:' . $res, Color::FG_GREEN) . PHP_EOL;

        $res = preg_replace($match, '_xx.png', $name);
        echo Color::colorize('正则替换结果:' . $res, Color::FG_GREEN) . PHP_EOL;
    }

    public function matchAction()
    {
        $msg = "字符串为 %s, 其中%s";

        $str = '11222djfee';
        if (preg_match("/[\x7f-\xff]/", $str)) {
            echo Color::colorize(sprintf($msg, $str, '包含汉字'), Color::FG_GREEN) . PHP_EOL;
        } else {
            echo Color::colorize(sprintf($msg, $str, '不包含汉字'), Color::FG_GREEN) . PHP_EOL;
        }

        $str = '11222djf黎ee';
        if (preg_match("/[\x7f-\xff]/", $str)) {
            echo Color::colorize(sprintf($msg, $str, '包含汉字'), Color::FG_GREEN) . PHP_EOL;
        } else {
            echo Color::colorize(sprintf($msg, $str, '不包含汉字'), Color::FG_GREEN) . PHP_EOL;
        }

        $str = '1122 2djf黎ee';
        if (preg_match("/[ ]/", $str)) {
            echo Color::colorize(sprintf($msg, $str, '包含空格'), Color::FG_GREEN) . PHP_EOL;
        } else {
            echo Color::colorize(sprintf($msg, $str, '不包含空格'), Color::FG_GREEN) . PHP_EOL;
        }

        $str = '11222djf黎ee';
        if (preg_match("/[ ]/", $str)) {
            echo Color::colorize(sprintf($msg, $str, '包含空格'), Color::FG_GREEN) . PHP_EOL;
        } else {
            echo Color::colorize(sprintf($msg, $str, '不包含空格'), Color::FG_GREEN) . PHP_EOL;
        }

        $str = 'aaassdd';
        if (preg_match("/d(d|s)$/", $str, $ss)) {
            echo Color::colorize(sprintf($msg, $str, '以dd或s结尾'), Color::FG_GREEN) . PHP_EOL;
        } else {
            echo Color::colorize(sprintf($msg, $str, '不以dd或s结尾'), Color::FG_GREEN) . PHP_EOL;
        }
    }

    public function camelizeAction()
    {
        $str = 'HelloWorld';
        echo Color::colorize("Text::uncamelize($str)=" . Text::uncamelize($str)) . PHP_EOL;
        $str = 'HelloWorld';
        echo Color::colorize("Text::uncamelize($str,'-')=" . Text::uncamelize($str, '-')) . PHP_EOL;

        $str = 'hello_world';
        echo Color::colorize("Text::camelize($str,'-')=" . Text::camelize($str, '-')) . PHP_EOL;

        $str = 'hello_world';
        echo Color::colorize("Text::camelize($str)=" . Text::camelize($str)) . PHP_EOL;

        $str = 'hello_world';
        echo Color::colorize("lcfirst(Text::camelize($str))=" . lcfirst(Text::camelize($str))) . PHP_EOL;

        $str = 'hello _world';
        echo Color::colorize("lcfirst(Text::camelize($str))=" . lcfirst(Text::camelize($str))) . PHP_EOL;

    }

    public function textRandomAction()
    {
        $str = Text::random(Text::RANDOM_NUMERIC, 12);
        echo Color::colorize("RANDOM_NUMERIC " . $str, Color::FG_GREEN) . PHP_EOL;

        $str = Text::random(Text::RANDOM_ALNUM, 12);
        echo Color::colorize("RANDOM_ALNUM " . $str, Color::FG_GREEN) . PHP_EOL;

        $str = Text::random(Text::RANDOM_ALPHA, 12);
        echo Color::colorize("RANDOM_ALPHA " . $str, Color::FG_GREEN) . PHP_EOL;

        $str = Text::random(Text::RANDOM_DISTINCT, 12);
        echo Color::colorize("RANDOM_DISTINCT " . $str, Color::FG_GREEN) . PHP_EOL;

        $str = Text::random(Text::RANDOM_HEXDEC, 12);
        echo Color::colorize("RANDOM_HEXDEC " . $str, Color::FG_GREEN) . PHP_EOL;

        $str = Text::random(Text::RANDOM_NOZERO, 12);
        echo Color::colorize("RANDOM_NOZERO " . $str, Color::FG_GREEN) . PHP_EOL;
    }

    public function strstrAction()
    {
        $strs = [
            'N_asdasdf',
            'N_0',
            'N_0000000000000',
            'N_1234567',
            'N_0aafassdfasdf',
        ];
        foreach ($strs as $str) {
            $res = strstr($str, 'N_');
            echo Color::colorize('原' . $str, Color::FG_GREEN) . PHP_EOL;
            echo Color::colorize('结果' . $res, Color::FG_GREEN) . PHP_EOL;
            if ($res) echo Color::colorize('成功', Color::FG_BLUE) . PHP_EOL;
        }
    }

    public function strPadAction()
    {
        $num = rand(1, 9999);
        echo Color::colorize("数字：" . $num, Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize("结果：" . str_pad($num, 5, '0', STR_PAD_LEFT));
    }

    public function randomAction($params)
    {
        $num = rand(1, 10);
        if (count($params) > 0) {
            $num = intval($params[0]);
        }
        echo Color::colorize(Str::random($num), Color::FG_GREEN) . PHP_EOL;
    }
}