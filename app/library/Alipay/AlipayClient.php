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
use AlipayAcquireCreateandpayRequest;

class AlipayClient
{
    public $appId;

    public $redirectUri;

    public $aliPublicKey;

    public $appPrivateKey;

    public $sellerId;

    public $parterId;

    protected $gatewayUrl = 'https://openapi.alipay.com/gateway.do';

    protected $signType = 'RSA2';

    protected $postCharset = 'UTF-8';

    protected $apiVersion = '1.0';

    protected $format = 'json';

    protected $aopClient;

    public static $instances;


    public function __construct()
    {
        $this->appId = env("MONSTER_ALIPAY_APPID");
        $this->redirectUri = env("MONSTER_ALIPAY_REDIRECT_URI");
        $this->aliPublicKey = env("MONSTER_ALIPAY_ALI_PUBLIC_KEY");
        $this->appPrivateKey = env("MONSTER_ALIPAY_APP_PRIVATE_KEY");
        $this->sellerId = env("MONSTER_ALIPAY_SELLERID");
        $this->parterId = env("MONSTER_ALIPAY_PID");

        include __DIR__ . '/AopSdk.php';

        $this->aopClient = $this->getAopClient();
    }

    public static function getInstance()
    {
        if (!isset(self::$instances) || !(self::$instances instanceof AlipayClient)) {
            self::$instances = new AlipayClient();
        }
        return self::$instances;
    }

    public function getAopClient()
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

        return $aop;
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

    /**
     * @desc   创建支付订单
     * @author limx
     * @return string|\提交表单HTML文本
     */
    public function getPaymentOrder($order_no, $real_price, $notifyUrl, $returnUrl)
    {
        $req = new AlipayTradeWapPayRequest();
        $data['out_trade_no'] = $order_no;
        $data['total_amount'] = $real_price;
        $data['subject'] = '测试支付';
        $data['seller_id'] = $this->sellerId;
        $data['product_code'] = 'QUICK_WAP_PAY';
        $bizContent = json_encode($data);
        $req->setBizContent($bizContent);
        $req->setNotifyUrl($notifyUrl);
        $req->setReturnUrl($returnUrl);

        // return $this->aopClient->pageExecute($req, "GET");
        return $this->aopClient->pageExecute($req, "POST");
    }


    /**
     * @desc   代扣签约并扣款
     * @author limx
     * @param $returnUrl      签约成功后会把签约结果同步返回给客户端。
     * @param $requestFromUrl 如果用户中途取消支付会返回该地址(唤起app)。
     */
    public function withholdingCreateAndPay($out_trade_no, $totalFee, $returnUrl, $notifyUrl, $requestFromUrl)
    {
        // https://mapi.alipay.com/gateway.do?_input_charset=utf-8
        // &agreement_sign_parameters={"productCode":"GENERAL_WITHHOLDING_P","scene":"INDUSTRY|GAME_CHARGE"
        // ,"externalAgreementNo":"201601010001","notifyUrl":"http://test.xxx.com/result/result.ashx"}
        // &integration_type=ALIAPP&notify_url=http://test.xxx.com/result.aspx&out_trade_no=201601010001x
        // &partner=2088101568351631&product_code=GENERAL_WITHHOLDING&request_from_url=myapp://result
        // &return_url=myapp://result&seller_id=2088101568351631&service=alipay.acquire.page.createandpay
        // &subject=test&total_fee=0.01&sign=53d0e696c8e755199ffa188e3f52b353&sign_type=MD5


        $gatway_url = 'https://mapi.alipay.com/gateway.do';
        $data['_input_charset'] = $this->postCharset;
        $data['agreement_sign_parameters'] = json_encode([
            'productCode' => 'GENERAL_WITHHOLDING_P',
            'scene' => 'INDUSTRY|GAME_CHARGE',
            'externalAgreementNo' => '201601010001',
            'notifyUrl' => $notifyUrl,
        ]);
        $data['integration_type'] = "ALIAPP";
        $data['notify_url'] = $notifyUrl;
        $data['out_trade_no'] = $out_trade_no;
        $data['partner'] = $this->parterId;
        $data['product_code'] = 'GENERAL_WITHHOLDING';
        $data['request_from_url'] = $requestFromUrl;
        $data['return_url'] = $returnUrl;
        $data['seller_id'] = $this->sellerId;
        $data['service'] = 'alipay.acquire.page.createandpay';
        $data['subject'] = "测试";
        $data['total_fee'] = $totalFee;
        $data["sign"] = $this->aopClient->generateSign($data, $this->signType);

        return $gatway_url . "?" . http_build_query($data);
    }

    public function sign($data)
    {

    }

    public function verify($data)
    {
        $result = $this->aopClient->rsaCheckV1($data, $this->aliPublicKey, $this->signType);
        return $result;
    }
}