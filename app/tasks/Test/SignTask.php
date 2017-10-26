<?php

namespace App\Tasks\Test;

use App\Support\Sign\Md5;
use App\Tasks\Task;
use limx\Support\Str;

class SignTask extends Task
{

    public function mainAction()
    {
        $url = 'http://demo.phalcon.lmx0536.cn/test/sign/verify';

        $body = [
            'id' => 1,
            'name' => 'limx',
            'book' => [
                'name' => '三天放弃php',
                'price' => 88,
                'author' => 'limx',
                'desc' => '中文',
                'text' => null,
            ],
            'text' => null,
        ];

        $header = [
            'X-APP-MAC' => Str::random(12),
            'X-APP-VERSION' => 'v1.0',
            'X-APP-TOKEN' => Str::random(32),
            'X-APP-PLATFORM' => 1,
            'X-APP-KEY' => 2,
            'X-APP-NONCE' => Str::random(64),
            'X-APP-UUIDS' => Str::random(64),
            'X-APP-TIMES' => microtime(true),
        ];

        $sign_body = $body;
        $sign_body['xappmac'] = $header['X-APP-MAC'];
        $sign_body['xappversion'] = $header['X-APP-VERSION'];
        $sign_body['xapptoken'] = $header['X-APP-TOKEN'];
        $sign_body['xappplatform'] = $header['X-APP-PLATFORM'];
        $sign_body['xappkey'] = $header['X-APP-KEY'];
        $sign_body['xappnonce'] = $header['X-APP-NONCE']; // 0
        $sign_body['xappuuids'] = $header['X-APP-UUIDS']; // 1
        $sign_body['xapptimes'] = $header['X-APP-TIMES']; // 2

        $header['X-APP-SIGN'] = (new Md5())->sign($sign_body);

        $res = $this->httpPost($url, $body, $header);
        dd($res);
    }

    public function httpPost($url, $body, $header)
    {
        $body = json_encode($body);

        $ch = curl_init();
        // 设置抓取的url
        curl_setopt($ch, CURLOPT_URL, $url);
        // 启用时会将头文件的信息作为数据流输出。
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 启用时将获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        // 设置访问 方法
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        // 设置POST BODY
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        // 设置header
        foreach ($header as $key => $item) {
            $headers[] = $key . ':' . $item;
        }

        $headers = array_merge($headers, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($body),
        ]);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        //执行命令
        $result = curl_exec($ch);
        if ($result === false) {
            echo 'Curl error: ' . curl_error($ch);
            return false;
        }
        //关闭URL请求
        curl_close($ch);
        $res = json_decode($result, true);
        return $res;
    }

}

