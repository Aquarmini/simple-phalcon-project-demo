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
        $client = AlipayClient::getInstance();
        $code = $this->request->get('auth_code');
        if (empty($code)) {
            $redirect_url = $this->redirectUrl . "/test/alipay/userInfo";
            $url = $client->getOauthCodeUrl($redirect_url);
            return $this->response->redirect($url);
        }

        $oauth_info = $client->getOauthInfo($code);

        dump($this->request->get());
        dump($oauth_info);

        $access_token = $oauth_info->access_token;
        $user_id = $oauth_info->user_id;
        $userinfo = $client->getUserInfo($access_token);

        dump($userinfo);
    }

}

