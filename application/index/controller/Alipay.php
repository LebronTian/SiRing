<?php

namespace app\index\controller;
use think\Controller;
use think\Loader;

class AliPay extends Controller
{

    protected $appId = '2018082761132725'; //支付宝AppId
    protected $rsaPrivateKey = 'MIIEowIBAAKCAQEAq2KGCUdWRFqi0KaALNAlPulSp2gMViM8E05Srq/r3EOux8ZMLIBYdTuDtTjfnfEdGg450S19FRh4i4CBxF0ToprIVVbXSacriUQB8t6Pu9O4GqAqOKm5uFlUV+GsBt6ImHFkBU8Azik0fAF6saer1RTAYuYfy2+jhRwISo5bvdQYGaWEnOmgwzZ0EV2/B/KxmGwmCNSnrrPxit7jlWCvOk5UPKfNeA9y+39w++EEHXafFGNO8YlR85LBpwyAZhdjcjA1pi8L1LeJF3O/965QEfAxreEUs0yshIInrVeAsj+gz8Q2qJPQkRow6lM1irELP061ZXZKP9+kKqTpt7iioQIDAQABAoIBAQCPXT1OabRKPZ9Q9tblpcBiXf9cNneLXrIUXEJiCps8iAme58w0tbBJcN1+LPMyRc3YS+olhu3JRc0gtQDYaBvSu7O1X417+TE8A/21UmPd9P9elnh7Kc9H3MHnOcoTfPe6va+zmSDNVD6pNPuTvPTKrC87C9Gw9dRNtuNgqrEnmvOCUE81WmCQwX7iNuxmXXqfsPNpjv2VbrVDW/rRKBngBHWzXRFbck+F8Hf3sBvSPc1zHe89D5IknjhMG213Tk1QQr9qKxUuIQKuhtsQHvg+LtuojDL7lFn6U+4iy8N5lFxoYvslpNAvYf48c5jZbZlp+VC37+vTbbuS+wNvPaP5AoGBANXkBJH43IQ0NuTFj82W4umF+IHSaM92z0FeBqYD3XapivcxValIxWYbzby+6xZjmSECxRLgPTBCZZnqAi+U5pfgXllXZ4RtbBHslI51TF/5S4JZrzZFF0JhjvNjYMLuj2JIYy1enrAqxUq3O8PJDf7lCflnOnPIt2bXEssCJjUTAoGBAM0gO7F0N5izaXgizkbHPtQRUHblVd+J02XaQroACgKvuK47UkTLky6UBuz+D1yjW/UkDCW7jKH1Fxdrzuak9e+ZGql8/O8l9yMUE6UY82yOUqiNIySZCrehiRkeEcvGxW0GmiE0Nz2JmJek/qIQWxznZDkpf/NTtQsy8iBOGyP7AoGAa2Tjzo9P2amF7nQr8iRSpsI3tqd5LMIQ6ldVq0HBjvUt61QAGNGLG+vV73FFBKbZmjOT1Bh3YKXV8eQHWPDAn31uohk6xslSO+W36ZeH06COg1KYoP0r4o6tghNh4D58C/MgqQUbVIUFLrC192YZ+uPxkCJ+vOgI/j/7Fadsm7UCgYAaWdCxTC+0MyASac47826TyaGflHiCne8FP5Og105x5+b+ouo/ojNHIYb+POj2SpoOlNHmqwA28ghEXvoWUQyy+eUd7suDYUotPHAFnn3u7R2bP35LPknNKzg0fNNmbSOBjP6R02ZhRmLO4EhBw34g6WzLlxQzywYP6TyHf+EmrwKBgBqJs9lZmLySy7FwM21fNVsJeKpMYH8hVvHi+xcafHRy7L7qz3K7Kl4QN4wHU+xCFkeRBRg6FybOBhpVurwGr1epg5UgYnnu1x3Y2Gmm75CCWpbkLQZqopGkWnRKNguZY65IXnVPEBCefugoWWxd7u/fNfDLKO1Sb6Z3FnHNQthK';//支付宝私钥
    protected $aliPayRsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq2KGCUdWRFqi0KaALNAlPulSp2gMViM8E05Srq/r3EOux8ZMLIBYdTuDtTjfnfEdGg450S19FRh4i4CBxF0ToprIVVbXSacriUQB8t6Pu9O4GqAqOKm5uFlUV+GsBt6ImHFkBU8Azik0fAF6saer1RTAYuYfy2+jhRwISo5bvdQYGaWEnOmgwzZ0EV2/B/KxmGwmCNSnrrPxit7jlWCvOk5UPKfNeA9y+39w++EEHXafFGNO8YlR85LBpwyAZhdjcjA1pi8L1LeJF3O/965QEfAxreEUs0yshIInrVeAsj+gz8Q2qJPQkRow6lM1irELP061ZXZKP9+kKqTpt7iioQIDAQAB';//支付宝公钥

    /*
    * 支付宝支付
    */
    public function aliPay($body, $total_amount, $product_code, $notify_url)
    {
        /**
         * 调用支付宝接口。
         */
        /*import('.Alipay.aop.AopClient', '', '.php');
        import('.Alipay.aop.request.AlipayTradeAppPayRequest', '', '.php');*/
        Loader::import('Alipay\aop\AopClient', EXTEND_PATH);
        Loader::import('Alipay\aop\request\AlipayTradeAppPayRequest', EXTEND_PATH);

        $aop = new \AopClient();

        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->appId;
        $aop->rsaPrivateKey = $this->rsaPrivateKey;
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayrsaPublicKey = $this->aliPayRsaPublicKey;
        $request = new \AlipayTradeAppPayRequest();
        $arr['body'] = $body;
        $arr['subject'] = $body;
        $arr['out_trade_no'] = $product_code;
        $arr['timeout_express'] = '30m';
        $arr['total_amount'] = floatval($total_amount);
        $arr['product_code'] = 'QUICK_MSECURITY_PAY';

        $json = json_encode($arr);
        $request->setNotifyUrl($notify_url);
        $request->setBizContent($json);

        $result = $aop->pageExecute ($request);

        return $result;

    }



    function createLinkstring($para)
    {
        $arg = "";
        while (list ($key, $val) = each($para)) {
            $arg .= $key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }


    function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

}

