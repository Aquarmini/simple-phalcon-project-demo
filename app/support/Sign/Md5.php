<?php
// +----------------------------------------------------------------------
// | Md5.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Support\Sign;

use App\Utils\Log;

class Md5
{
    public function sign($params, $key = null)
    {
        if (!isset($key)) {
            $key = array_rand(['xappnonce' => 0, 'xappuuids' => 1, 'xapptimes' => 2]);
        }

        $sign_array = $this->recursiveMapToArray($params);
        sort($sign_array);
        $str = implode('&', $sign_array);
        Log::info($str);
        $sign = md5($str . $params[$key]);
        $new_sign = substr_replace($sign, $key, 2, 0);
        Log::info($new_sign);
        return $new_sign;
    }

    public function verify($params, $sign)
    {
        $key = substr($sign, 2, 9);
        if (!in_array($key, ['xappnonce', 'xappuuids', 'xapptimes'])) {
            return false;
        }
        return $this->sign($params, $key) === $sign;
    }

    public function recursiveMapToArray($params = [])
    {
        $result = [];
        if (empty($params)) {
            return $result;
        }

        foreach ($params as $key => $item) {
            if (is_array($item)) {
                $result = array_merge($result, $this->recursiveMapToArray($item));
            } else if (!isset($item)) {

            } else {
                $result[] = $key . '=' . $item;
            }
        }

        return $result;
    }
}