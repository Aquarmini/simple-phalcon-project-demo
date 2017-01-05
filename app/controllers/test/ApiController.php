<?php

namespace MyApp\Controllers\Test;

use Gregwar\Captcha\CaptchaBuilder;
use limx\func\Curl;

class ApiController extends ControllerBase
{

    public function indexAction()
    {
        return $this->view->render('test/api', 'index');
    }

    public function yunpianAction()
    {
        return $this->view->render('test/api', 'yunpian');
    }

    public function captchaAction()
    {
        /** composer require gregwar/captcha */
        $builder = new CaptchaBuilder;
        $builder->build();
        session('captcha', $builder->getPhrase());
        header('Content-type: image/jpeg');
        return $builder->output();
    }

    public function sendMsgAction($code = '')
    {
        if ($code == session('captcha')) {
            $url = 'https://sms.yunpian.com/v2/sms/single_send.json';
            $mobile = '18678017521';
            $address = "山东省潍坊市奎文区";
            $text = "【佑骏信息】有人下单了，快去看看吧。查询地址是{$address}。";
            $apikey = env('YUNPIAN_APIKEY');
            $data = ['text' => $text, 'apikey' => $apikey, 'mobile' => $mobile];
            $res = Curl::post($url, $data);
            $arr = json_decode($res, true);
            return success([$arr]);
        }
        return error('发送失败');
    }

    public function huanxinAction()
    {
        $option = [
            'client_id' => env('EASEMOB_ID'),
            'client_secret' => env('EASEMOB_SECRET'),
            'org_name' => env('EASEMOB_ORG_NAME'),
            'app_name' => env('EASEMOB_APP_NAME'),
        ];
        $easemob = new \limx\tools\Easemob($option);
//        $easemob->userAuthorizedRegister('limx2', '910123');
        $res = $easemob->sendToUsers(['pt942', '942'], '948', ['msg' => 'lalala']);
        dump($res);
    }

}

