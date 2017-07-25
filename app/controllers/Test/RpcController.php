<?php

namespace App\Controllers\Test;

use App\Controllers\Controller;
use App\Logics\Test\ThriftRegister;
use App\Logics\Test\YarServer;
use Yar_Server;

class RpcController extends Controller
{

    public function yarAction()
    {
        if (!extension_loaded('yar')) {
            return self::error('The yar extension is not installed');
        }
        $service = new Yar_Server(new YarServer());
        $service->handle();
    }

    public function thriftAction()
    {
        ThriftRegister::register();
        ThriftRegister::handle();
    }

}

