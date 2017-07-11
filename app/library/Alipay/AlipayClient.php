<?php
// +----------------------------------------------------------------------
// | Alipay.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Library\Alipay;

use AopClient;
use AlipaySystemOauthTokenRequest;
use AlipayTradeWapPayRequest;

class AlipayClient
{
    public $appId;

    public $redirectUri;

    public $aliPublicKey;

    public $appPrivateKey;

    public $sellerId;

    protected $gatewayUrl = 'https://openapi.alipay.com/gateway.do';

    protected $signType = 'RSA2';

    protected $postCharset = 'UTF-8';

    protected $apiVersion = '1.0';

    protected $format = 'json';

    public static $instances;

    public function __construct()
    {
        $this->appId = env("MONSTER_ALIPAY_APPID");
        $this->redirectUri = env("MONSTER_ALIPAY_REDIRECT_URI");
        $this->aliPublicKey = env("MONSTER_ALIPAY_ALI_PUBLIC_KEY");
        $this->appPrivateKey = env("MONSTER_ALIPAY_APP_PRIVATE_KEY");
        $this->sellerId = env("MONSTER_ALIPAY_SELLERID");

        include __DIR__ . '/AopSdk.php';

    }

    public static function getInstance()
    {
        if (!isset(self::$instances) || !(self::$instances instanceof AlipayClient)) {
            self::$instances = new AlipayClient();
        }
        return self::$instances;
    }

    public function getOauthCodeUrl($redirect_uri)
    {
        $url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?';
        $params = [
            'app_id' => $this->appId,
            'scope' => 'auth_user',
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
        $aop = new AopClient();
        $aop->gatewayUrl = $this->gatewayUrl;
        $aop->appId = $this->appId;
        $aop->rsaPrivateKey = $this->appPrivateKey;
        $aop->alipayrsaPublicKey = $this->aliPublicKey;
        $aop->signType = $this->signType;
        $aop->postCharset = $this->postCharset;
        $aop->apiVersion = $this->apiVersion;
        $aop->format = $this->format;

        $request = new AlipaySystemOauthTokenRequest();
        $request->setGrantType("authorization_code");
        $request->setCode($authCode);
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";

        return $result->$responseNode;

    }

    /**
     * @desc   创建支付订单
     * @author limx
     * @return string|\提交表单HTML文本
     */
    public function getPaymentOrder($order_no, $real_price, $priceId)
    {
        $aop = new AopClient();
        $aop->gatewayUrl = $this->gatewayUrl;
        $aop->appId = $this->appId;
        $aop->rsaPrivateKey = $this->appPrivateKey;
        $aop->signType = $this->signType;
        $aop->postCharset = $this->postCharset;
        $aop->format = $this->format;

        $aop->alipayrsaPublicKey = $this->aliPublicKey;

        $req = new AlipayTradeWapPayRequest();
        $data['out_trade_no'] = $order_no;
        $data['total_amount'] = $real_price / 100;
        $data['subject'] = '测试支付';
        $data['seller_id'] = $this->sellerId;
        $data['product_code'] = 'QUICK_WAP_PAY';
        $bizContent = json_encode($data);
        $req->setBizContent($bizContent);
        $req->setNotifyUrl('https://m.emmars.cn/payment/notify/alipay');
        $req->setReturnUrl('https://open.emmars.cn/pay?isPay=true&priceId=' . $priceId);

        return $aop->pageExecute($req, "GET");
    }

    public function verify($data)
    {
        $aop = new AopClient();
        $aop->gatewayUrl = $this->gatewayUrl;
        $aop->appId = $this->appId;
        $aop->rsaPrivateKey = $this->appPrivateKey;
        $aop->alipayrsaPublicKey = $this->aliPublicKey;
        $aop->signType = $this->signType;
        $aop->postCharset = $this->postCharset;
        $aop->format = $this->format;

        $result = $aop->rsaCheckV1($data, $this->aliPublicKey, $this->signType);
        return $result;
    }

}