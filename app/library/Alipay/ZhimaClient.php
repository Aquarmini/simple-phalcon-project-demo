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
use AopClient;

class ZhimaClient
{
    public $appId;

    public $redirectUri;

    public $aliPublicKeyFile;

    public $appPrivateKeyFile;

    public $aliPublicKey;

    public $appPrivateKey;

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
        // $this->aliPublicKeyFile = ROOT_PATH . '/data/alipay/zhima/ali_public_key.pem';
        // $this->appPrivateKeyFile = ROOT_PATH . '/data/alipay/zhima/rsa_private_key.pem';
        $this->aliPublicKeyFile = env('MONSTER_ZHIMA_ALI_PUBLIC_KEY');
        $this->appPrivateKeyFile = env('MONSTER_ZHIMA_APP_PRIVATE_KEY');
        $this->aliPublicKey = env('MONSTER_ZHIMA_ALI_PUBLIC_KEY');
        $this->appPrivateKey = env('MONSTER_ZHIMA_APP_PRIVATE_KEY');

        include_once __DIR__ . '/ZmopSdk.php';

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

        // $aop = new AopClient();
        // $aop->gatewayUrl = $this->gatewayUrl;
        // $aop->appId = $this->appId;
        // $aop->rsaPrivateKey = $this->appPrivateKey;
        // $aop->alipayrsaPublicKey = $this->aliPublicKey;
        // $aop->signType = $this->signType;
        // $aop->postCharset = $this->postCharset;
        // $aop->apiVersion = $this->apiVersion;
        // $aop->format = $this->format;

        return $client;
    }

    public function getAuthInfoByMobile($mobile)
    {
        $request = new \ZhimaAuthInfoAuthorizeRequest();
        $request->setChannel("apppc");
        $request->setPlatform("zmop");
        $request->setIdentityType("1");// 必要参数
        $request->setIdentityParam(json_encode([
            'mobileNo' => $mobile
        ]));
        $request->setBizParams(json_encode([
            'auth_code' => 'M_H5',
            'channelType' => 'app',
        ]));
        $url = $this->aopClient->generatePageRedirectInvokeUrl($request);
        return $url;
    }


    public function getCreditScore($accessToken)
    {
        $request = new \ZhimaCreditScoreGetRequest();
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