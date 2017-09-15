<?php

namespace App\Tasks\Swoole;

use App\Tasks\Task;
use swoole_client;

class ClientTask extends Task
{

    public function mainAction()
    {

    }

    public function socketAction()
    {
        $client = new swoole_client(SWOOLE_SOCK_TCP);
        if (!$client->connect('127.0.0.1', 11520, -1)) {
            exit("connect failed. Error: {$client->errCode}\n");
        }
        $client->send("hello world\n");
        echo $client->recv();
        $client->close();
    }

}

