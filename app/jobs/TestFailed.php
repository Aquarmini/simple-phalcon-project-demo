<?php
// +----------------------------------------------------------------------
// | TestFailed.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Jobs;

use App\Jobs\Contract\JobInterface;
use App\Utils\Log;
use Exception;
use limx\phalcon\Cli\Color;

class TestFailed implements JobInterface
{
    public $msg;

    public function __construct($msg)
    {
        $this->msg = $msg;
    }

    public function handle()
    {
        echo Color::colorize('错误抛出', Color::FG_RED) . PHP_EOL;
        throw new Exception('错误');
    }

}