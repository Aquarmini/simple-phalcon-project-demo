<?php

namespace App\Tasks\Test;

use App\Tasks\Task;
use limx\phalcon\Cli\Color;
use limx\Support\Str;

class SmsTask extends Task
{

    public function mainAction()
    {
        echo Color::head('Help:'), PHP_EOL;
        echo Color::colorize('  短信测试'), PHP_EOL, PHP_EOL;

        echo Color::head('Usage:'), PHP_EOL;
        echo Color::colorize('  php run Test\\\\Sms [action]', Color::FG_GREEN), PHP_EOL, PHP_EOL;

        echo Color::head('Actions:'), PHP_EOL;
        echo Color::colorize('  baiwu        百悟', Color::FG_GREEN), PHP_EOL;
        echo Color::colorize('  group        查询Group方法', Color::FG_GREEN), PHP_EOL;
    }

    public function baiwuAction()
    {
        $url = 'http://client.cloud.hbsmservice.com:8080/sms_send2.do';
        $id = env('BAIWU_ID');
        $password = env('BAIWU_PASSWORD');
        $code = env('BAIWU_SERVICE');

        $data = [
            'corp_id' => $id,
            'corp_pwd' => $password,
            'corp_service' => $code,
            'mobile' => '18678017521',
            'msg_content' => mb_convert_encoding('测试代码，哈哈哈', "GBK", "auto"),
            'corp_msg_id' => 'test' . date('YmdHis') . Str::random(16),
            'ext' => 8888
        ];

        $result = $this->httpPost($url, $data);
        dd($result);
    }

    private function httpPost($url, $data, $header = null)
    {

        $body = http_build_query($data);

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
        // curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //     'APPID:' . uniqid(),
        //     'APPSECRET:' . md5(uniqid()),
        // ]);

        //执行命令
        $result = curl_exec($ch);
        if ($result === false) {
            $msg = 'Curl error: ' . curl_error($ch);
            return [false, $msg];
        }
        //关闭URL请求
        curl_close($ch);
        $res = json_decode($result, true);
        return [true, $res];
    }


}

