<?php

namespace App\Controllers\Test;

use App\Controllers\Controller;
use App\Logics\Test\YarServer;
use Yar_Server;

class YarController extends Controller
{

    public function indexAction()
    {
        if (!extension_loaded('yar')) {
            return self::error('The yar extension is not installed');
        }
        $service = new Yar_Server(new YarServer());
        $service->handle();
    }

}

