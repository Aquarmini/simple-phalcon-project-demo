<?php

namespace App\Tasks\Test;

use App\Logics\Test\ThriftRegister;
use Xin\Cli\Color;
use limx\Support\Str;
use Yar_Client;

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;

class RpcTask extends \Phalcon\Cli\Task
{

    public function mainAction()
    {
        if (!extension_loaded('yar')) {
            echo Color::error('The yar extension is not installed');
            return;
        }

        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  Rpc扩展测试') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run Test\\\\Yar [action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Actions:') . PHP_EOL;
        echo Color::colorize('  yar         调用hello方法', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  run         数据量极大的RPC调用', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  thrift      调用hello方法', Color::FG_GREEN) . PHP_EOL;

    }

    public function thriftAction()
    {
        ThriftRegister::register();
        $socket = new THttpClient('demo.phalcon.app', 80, '/test/rpc/thrift');
        // $socket = new TSocket('localhost', 9090);

        $transport = new TBufferedTransport($socket, 1024, 1024);
        $protocol = new TBinaryProtocol($transport);
        $client = new \HelloThrift\HelloServiceClient($protocol);

        $transport->open();

        echo $client->sayHello(" World! ");

        $transport->close();
    }

    public function yarAction()
    {
        $client = new Yar_Client("http://demo.phalcon.app/test/rpc/yar");
        /* the following setopt is optinal */
        $client->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 1000);

        /* call remote service */
        $result = $client->hello("parameter");
        print_r($result);
    }

    public function runAction()
    {
        $params = [];
        for ($i = 0; $i < 10000; $i++) {
            $params['key'][] = Str::random(12);
        }

        for ($i = 0; $i < 10000; $i++) {
            $params['val'][] = Str::random(12);
        }

        $time = microtime(true);
        foreach ($params['key'] as $item) {
            foreach ($params['val'] as $item2) {

            }
        }

        echo microtime(true) - $time;
        echo PHP_EOL;

        $time = microtime(true);
        $client = new Yar_Client("http://demo.phalcon.app/test/yar");
        /* the following setopt is optinal */
        $client->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 1000);

        /* call remote service */
        $result = $client->run($params);
        echo microtime(true) - $time;
    }

}

