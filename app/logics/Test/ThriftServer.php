<?php
// +----------------------------------------------------------------------
// | YarServer.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Logics\Test;

class ThriftServer implements \HelloThrift\HelloServiceIf
{
    public function sayHello($username)
    {
        return "Hello " . $username;
    }

}