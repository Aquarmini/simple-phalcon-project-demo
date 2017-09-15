<?php

namespace App\Tasks\Swoole;

use App\Tasks\System\Socket;
use swoole_server;

class SocketTask extends Socket
{
    protected function events()
    {
        return [
            'connect' => [$this, 'connect'],
            'receive' => [$this, 'receive'],
            'close' => [$this, 'close'],
        ];
    }

    public function connect(swoole_server $server, $fd, $from_id)
    {
        echo 'connect' . PHP_EOL;
    }

    public function receive(swoole_server $server, $fd, $reactor_id, $data)
    {
        echo 'receive' . PHP_EOL;
        echo $data . PHP_EOL;
    }

    public function close(swoole_server $server, $fd, $reactorId)
    {
        echo 'close' . PHP_EOL;
    }

}

