<?php
// +----------------------------------------------------------------------
// | ZhimaClient.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Library\Alipay;

use ZmopClient;

class ZhimaClient
{
    public $appId;

    public $redirectUri;

    public $aliPublicKeyFile;

    public $appPrivateKeyFile;

    public $sellerId;

    public $parterId;

    protected $gatewayUrl = 'https://zmopenapi.zmxy.com.cn/openapi.do';

    protected $signType = 'RSA2';

    protected $postCharset = 'UTF-8';

    protected $apiVersion = '1.0';

    protected $format = 'json';

    protected $aopClient;

    public static $instances;


    public function __construct()
    {
        $this->appId = env("MONSTER_ZHIMA_APPID");
        $this->aliPublicKeyFile = ROOT_PATH . '/data/alipay/zhima/ali_public_key.pem';
        $this->appPrivateKeyFile = ROOT_PATH . '/data/alipay/zhima/rsa_private_key.pem';

        include_once __DIR__ . '/AopSdk.php';

        $this->aopClient = $this->getZmopClient();
    }

    public static function getInstance()
    {
        if (!isset(self::$instances) || !(self::$instances instanceof AlipayClient)) {
            self::$instances = new ZhimaClient();
        }
        return self::$instances;
    }

    public function getZmopClient()
    {
        $client = new ZmopClient(
            $this->gatewayUrl,
            $this->appId,
            $this->postCharset,
            $this->appPrivateKeyFile,
            $this->aliPublicKeyFile
        );

        return $client;
    }

    public function getOauthCodeUrl($redirect_uri, $scope = 'auth_user')
    {
        $url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?';
        $params = [
            'app_id' => $this->appId,
            'scope' => $scope,
            'redirect_uri' => $redirect_uri
        ];
        return $url . http_build_query($params);
    }

    /**
     * @desc   获取授权信息
     * @author limx
     * @param $authCode
     * @return \SimpleXMLElement[]
     */
    public function getOauthInfo($authCode)
    {
        $request = new AlipaySystemOauthTokenRequest();
        $request->setGrantType("authorization_code");
        $request->setCode($authCode);
        $result = $this->aopClient->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";

        return $result->$responseNode;

    }

    public function getUserInfo($accessToken)
    {
        $request = new \AlipayUserInfoShareRequest();
        $result = $this->aopClient->execute($request, $accessToken);
        return $result;
    }

    public function getCreditScore($accessToken)
    {
        $request = new ZhimaCreditScoreGetRequest();
        $data['transaction_id'] = Str::random(64);
        $data['product_code'] = 'w1010100100000000001';
        $request->setBizContent(json_encode($data));
        $result = $this->aopClient->execute($request, $accessToken);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        return $result->$responseNode;
    }

    public function verify($data)
    {
        $result = $this->aopClient->rsaCheckV1($data, $this->aliPublicKey, $this->signType);
        return $result;
    }
}