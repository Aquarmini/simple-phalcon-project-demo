<?php

namespace App\Jobs;

use App\Jobs\Contract\JobInterface;
use App\Utils\Queue;

class ErrorSync implements JobInterface
{
    public function handle()
    {
        try {
            throw new \Exception("报错了");
        } catch (\Exception $ex) {
            Queue::delay(new static(), 1);
        }
    }

}

