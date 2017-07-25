<?php
// +----------------------------------------------------------------------
// | ThriftRegister.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Logics\Test;

use Thrift\ClassLoader\ThriftClassLoader;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TPhpStream;
use Thrift\Transport\TBufferedTransport;

class ThriftRegister
{
    public static function register()
    {
        $gen_dir = ROOT_PATH . '/thrift/gen-php';
        $loader = new ThriftClassLoader();
        $loader->registerDefinition('HelloThrift', $gen_dir);
        $loader->register();
    }

    public static function handle()
    {
        header('Content-Type', 'application/x-thrift');

        $handler = new ThriftServer();
        $processor = new \HelloThrift\HelloServiceProcessor($handler);

        $transport = new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));
        $protocol = new TBinaryProtocol($transport, true, true);

        $transport->open();
        $processor->process($protocol, $protocol);
        $transport->close();
    }
}