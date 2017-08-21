<?php

namespace App\Jobs;

use App\Jobs\Contract\JobInterface;
use App\Utils\Log;
use App\Utils\Queue;
use App\Utils\Redis;

class DelaySync implements JobInterface
{
    public function __construct()
    {
    }

    public function handle()
    {
        if (Redis::incr('phalcon:test:delay:jobs') < 5) {
            Log::info('delaying...');
            echo 'delaying...' . PHP_EOL;
            Queue::delay(new static(), 1);
            return;
        }
        echo 'handle...' . PHP_EOL;
        Log::info('handle...');
    }

}

