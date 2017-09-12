<?php

namespace App\Jobs;

use App\Jobs\Contract\JobInterface;
use App\Utils\Log;
use App\Utils\Queue;
use App\Utils\Redis;

class DelaySync implements JobInterface
{
    public $count;

    public function __construct($count = 0)
    {
        $this->count = $count;
    }

    public function handle()
    {
        if ($this->count < 5) {
            Log::info('delaying...');
            echo $this->count . 'delaying...' . PHP_EOL;
            Queue::delay(new static(++$this->count), 1);
            return;
        }
        echo 'handle...' . PHP_EOL;
        Log::info('handle...');
    }

}

