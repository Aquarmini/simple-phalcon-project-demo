<?php

namespace App\Controllers\Test;

use App\Controllers\Controller;

class WechatController extends Controller
{
    protected $redirectUrl;

    protected $appUrl;

    public function initialize()
    {
        $this->redirectUrl = env("MONSTER_ALIPAY_REDIRECT_URI");
        $this->appUrl = env('APP_URL');
        parent::initialize();
    }

    public function indexAction()
    {
        $config = app('easywechat');
        dump($config);
        $app = new Application($config);
        $response = $app->oauth->scopes(['snsapi_userinfo'])->redirect();
        dump($response);
        $response->send();
    }

}

