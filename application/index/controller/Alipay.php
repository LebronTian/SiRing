<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/10/30
 * Time: 18:17
 */

namespace app\index\controller;
use  think\Config;
class Alipay
{

    /*
     * 支付宝支付
     * $body            名称
     * $total_amount    价格
     * $product_code    订单号
     * $notify_url      异步回调地址
     */
    public function alipay($body, $total_amount, $product_code, $notify_url)
    {

        /**
         * 调用支付宝接口。
         */

        import('.Alipay.aop.AopClient', '', '.php');
        import('.Alipay.aop.request.AlipayTradeAppPayRequest', '', '.php');

        $aop = new \AopClient();

        $aop->gatewayUrl            = Config::get('alipay')['gatewayUrl'];
        $aop->appId                 = Config::get('alipay')['appId'];
        $aop->rsaPrivateKey         = Config::get('alipay')['rsaPrivateKey'];
        $aop->format                = Config::get('alipay')['format'];
        $aop->charset               = Config::get('alipay')['charset'];
        $aop->signType              = Config::get('alipay')['signType'];
        $aop->alipayrsaPublicKey    = Config::get('alipay')['alipayrsaPublicKey'];

        $request = new \AlipayTradeAppPayRequest();
        $arr['body']                = $body;
        $arr['subject']             = $body;
        $arr['out_trade_no']        = $product_code;
        $arr['timeout_express']     = '30m';
        $arr['total_amount']        = floatval($total_amount);
        $arr['product_code']        = 'QUICK_MSECURITY_PAY';

        $json = json_encode($arr);
        $request->setNotifyUrl($notify_url);
        $request->setBizContent($json);

        $response = $aop->sdkExecute($request);
        return $response;

    }

}