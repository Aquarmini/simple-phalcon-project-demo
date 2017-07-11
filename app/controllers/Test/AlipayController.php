<?php

namespace App\Controllers\Test;

use App\Controllers\Controller;
use App\Library\Alipay\AlipayClient;
use App\Utils\Log;
use limx\Support\Str;

class AlipayController extends Controller
{
    protected $redirectUrl;

    public function initialize()
    {
        $this->redirectUrl = env("MONSTER_ALIPAY_REDIRECT_URI");
        parent::initialize();
    }

    /**
     * @desc   用户授权信息 个人信息
     * @author limx
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
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

    /**
     * @desc   支付
     * @author limx
     */
    public function paymentAction()
    {
        $client = AlipayClient::getInstance();
        $notify_url = $this->redirectUrl . "/test/alipay/notify";
        $return_url = $this->redirectUrl . "/test/alipay/return";
        $res = $client->getPaymentOrder("ORDER" . Str::random(12), 0.01, $notify_url, $return_url);

        echo $res;
    }

    /**
     * @desc   支付宝代扣 首次签约并扣款
     * @author limx
     */
    public function withholdingAction()
    {
        $client = AlipayClient::getInstance();
        $code = $this->request->get('auth_code');
        if (empty($code)) {
            $redirect_url = $this->redirectUrl . "/test/alipay/withholding";
            $url = $client->getOauthCodeUrl($redirect_url);
            return $this->response->redirect($url);
        }

        $oauth_info = $client->getOauthInfo($code);
        $user_id = $oauth_info->user_id;

        $return_url = $this->redirectUrl . "/test/alipay/return";
        $cancel_url = $this->redirectUrl . "/test/alipay/cancel";
        $notify_url = $this->redirectUrl . "/test/alipay/notify";
        $result = $client->withholdingCreateAndPay(
            $user_id, 0.01, $return_url, $notify_url, $cancel_url
        );

        dump($result);
    }

    public function cancelAction()
    {
        $data = $this->request->get();
        $data['ret'] = "CANCEL";
        dump($data);
    }


    public function returnAction()
    {
        $data = $this->request->get();
        $data['ret'] = "SUCCESS";
        dump($data);
    }

    public function notifyAction()
    {
        Log::info("DEBUG ALIPAY NOTIFY");
        $data = $this->request->get();
        Log::info("DEBUG ALIPAY " . json_encode($data));
        $result = AlipayClient::getInstance()->verify($data);

        /* 实际验证过程建议商户添加以下校验。
         1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
         2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
         3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
         4、验证app_id是否为该商户本身。
        */
        if ($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            Log::info("DEBUG ALIPAY VERIFIED " . json_encode($data));

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号

            $out_trade_no = $this->request->get('out_trade_no');

            //支付宝交易号

            $trade_no = $this->request->get('trade_no');

            //交易状态
            $trade_status = $this->request->get('trade_status');

            // 时间
            $now = time();


            if ($trade_status == 'TRADE_FINISHED') {

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            } else if ($trade_status == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知

                // TODO:修改支付宝支付订单状态
                Log::info('DEBUG ALIPAY ORDER TRADE_SUCCESS ' . $out_trade_no);

            } else {

            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";        //请不要修改或删除

        } else {
            //验证失败
            echo "fail";    //请不要修改或删除

        }
    }

}

