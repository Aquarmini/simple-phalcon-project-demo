<?php
// +----------------------------------------------------------------------
// | StaticFuncClass.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Test\Obj;

use App\Core\Support\InstanceBase;

class StaticFuncClass extends InstanceBase
{
    public function test1()
    {
        return 'I am no-static function';
    }

    public static function test2()
    {
        return 'I am static function';
    }
}