<?php

namespace App\Controllers\Test;

use App\Controllers\Controller;
use App\Library\Alipay\AlipayClient;

class AlipayController extends Controller
{
    protected $redirectUrl;

    public function initialize()
    {
        $this->redirectUrl = env("MONSTER_ALIPAY_REDIRECT_URI");
        parent::initialize();
    }

    public function userInfoAction()
    {
        $AliClient = AlipayClient::getInstance();
        $code = $this->request->get('auth_code');
        if (empty($code)) {
            $redirect_url = $this->redirectUrl . "/test/alipay/userInfo";
            $url = $AliClient->getOauthCodeUrl($redirect_url);
            return $this->response->redirect($url);
        }

        $userinfo = $AliClient->getOauthInfo($code);

        print_r($this->request->get());
        print_r($userinfo);
    }

}

