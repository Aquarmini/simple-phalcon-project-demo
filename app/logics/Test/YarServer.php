<?php
// +----------------------------------------------------------------------
// | YarServer.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Logics\Test;

class YarServer
{
    /**
     * the doc info will be generated automatically into service info page.
     * @params
     * @return
     */
    public function hello($parameter, $option = "foo")
    {
        return ['param' => $parameter, 'option' => $option];
    }

    public function run($params = [])
    {
        foreach ($params['key'] as $item) {
            foreach ($params['val'] as $item2) {

            }
        }
        return true;
    }

    protected function client_can_not_see()
    {

    }
}