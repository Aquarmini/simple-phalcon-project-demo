<?php

namespace App\Tasks\Test;

use limx\phalcon\Cli\Color;
use Yar_Client;

class YarTask extends \Phalcon\Cli\Task
{

    public function mainAction()
    {
        if (!extension_loaded('yar')) {
            echo Color::error('The yar extension is not installed');
            return;
        }

        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  Yar扩展测试') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run Test\\\\Yar [action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Actions:') . PHP_EOL;
        echo Color::colorize('  hello       调用hello方法', Color::FG_GREEN) . PHP_EOL;
    }

    public function helloAction()
    {
        $client = new Yar_Client("http://demo.phalcon.app/test/yar");
        /* the following setopt is optinal */
        $client->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 1000);

        /* call remote service */
        $result = $client->hello("parameter");
        print_r($result);
    }

}

