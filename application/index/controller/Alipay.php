<?php

namespace app\index\controller;
use think\Controller;
use think\Loader;
use think\Paginator;
use think\Request;
//use Alipay\wappay\buildermodel\AlipayTradeWapPayContentBuilder;
//use Alipay\wappay\service\AlipayTradeService;

class AliPay extends Controller
{
    /*
    * 支付宝支付
    */
    public function aliPay(Request $request)
    {
        $config = array (
            //应用ID,您的APPID。
            'app_id' => "2016112603335050",

            //商户私钥，您的原始格式RSA私钥
            'merchant_private_key' => "MIIEowIBAAKCAQEAz+SfWrndsOSD3AY3v5YtA9n+BoBcckMYfjgpIrT5Bu2YF2GR5oFCBJASSQeRRyDHPWL3i91lbyZeiBsE2l+rJcMTP+EfH6MpxMerwqfvOPw4p4OHHAnbI52xjdNZStBdIT7oEwEUsghuejCpWelL/b3CPFpW/1OpEVRnssw9gc0f1mius2eOXZ0+5JaJRZ/zJWxgyMHctF6NXcSG2oVOl0WyiNK/F4CuqdIcq1y8ZDiVvmRbyfzcEmbgob7MpwVFWw1Fge3z4fSnG7bicOJSXkPbWNhZmGe/yXCEXbA/8Kldp/nMkwnMGJ5A/3yFZTEUnmY60qnXA5T3R1KOnpXklwIDAQABAoIBAQCDq2VSbQ4AD3uES1vbuB3ipprBO2NR6zUEHEXReZWP0cPWazGhMJTDlww9vNFCn3wRYTEwIJUyBLcytQop1RXs4NS8TLUNsKWvwFcE/qABE54+WoukMonc0O+3x/hx7e5ONC2Ae9rDt5thQJjCHYTHvPvchcs8A5y9IRxcngcGweL6m6KUd4yT4yr5pPCXM8Q4B5cG/BM+MtLeqPJ1S7zheMKt4pN52M9pU9+n1V3nx1FgViv7ycOh8E+9L33S/Ri9HuLyIeV9zZ44g53ociUlSoQBnUIiDHWriHROWP0yxPdp0Et4oUPFcsDR1FVa8rFSmhZRauA6M7Um8SRXKVtBAoGBAPrhPqij8HfnOCJAGMcbwJnpQGZAypYBawEOSib3uIKyqEQmDlvzTjJgR2YbFUfGvgeAn0mX/Q4B/Vgffb1dqCJbU4McSE3GHJHCdBO6UqvUD4B8Qy6aJJomPGwgZAi+DAk9PtNDo2tC6DTZbd5UJMTqdpMq0776pjR6E3+7F2wZAoGBANQiyZTfw9qqf9xyQ4YKwu6v0165e+mnlycOTRkrBSESJUNSCH4aYHZnE4B9J1MU5fxxrZuk5qt6iu0N5AkUQY6xuLkKdjX8WJbHWgHHjvMxXsEqx1LQlQ2PSCCvF5jxB0xhzTjBa3uCzfabs3o+6MKh1QF1DuYMBE1B/rku8uwvAoGAdnuAIxbhj08EpLBOw2Ho8QdGocQBqRxcU7BS9tpRKnCDpUOvzl820/XCYodx4mcLAfINyCzelwn7gu3EbXVY3XjyFN57izd/8Jq8RUDeoEXTWGPXOqATnzVlnc8iTzqp5oclL5MnD5YWojb5e2GTx+fPPiuguvYXHnt00AMkyakCgYAkFOKqksDSUYu76Cd6BhyP0pImG3BrFplMCE+uxzVxIY/6+ln9cOkVWoTjpuXoaLaRkJhRz+N4KTi2B1XRAYQBDFN6DcB7gDdlNfUmNlYnIS+XtXn/qQChNMy02nMuDVkLcdshGyz37hCwMF1/nnGioToErG9jS4nzxhTYVJb2+wKBgGGU5XtXTZAeBtueAgwwPkdOe1pXHXjkeytG2cGeNJrBkMmj7B7eNt+3EkHw4yPgvj/e4OYNm4ojRH05FefZmb6dtLDUH0p1k9LeqEbGGbHn7cl7jDjTqaRznlODyaT3pJlRldZIaJ95VEwZtpMQCnItUAu5yGH3Vrgo2Y8eNpAn",

            //异步通知地址
            'notify_url' => "http://localhost/SiRing/public/Alipay_pay_code",

            //同步跳转
            'return_url' => "http://localhost/SiRing/public/Alipay_pay_code",

            //编码格式
            'charset' => "UTF-8",

            //签名方式
            'sign_type'=>"RSA2",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz+SfWrndsOSD3AY3v5YtA9n+BoBcckMYfjgpIrT5Bu2YF2GR5oFCBJASSQeRRyDHPWL3i91lbyZeiBsE2l+rJcMTP+EfH6MpxMerwqfvOPw4p4OHHAnbI52xjdNZStBdIT7oEwEUsghuejCpWelL/b3CPFpW/1OpEVRnssw9gc0f1mius2eOXZ0+5JaJRZ/zJWxgyMHctF6NXcSG2oVOl0WyiNK/F4CuqdIcq1y8ZDiVvmRbyfzcEmbgob7MpwVFWw1Fge3z4fSnG7bicOJSXkPbWNhZmGe/yXCEXbA/8Kldp/nMkwnMGJ5A/3yFZTEUnmY60qnXA5T3R1KOnpXklwIDAQAB",

        );

        //Loader::import("Alipay.wappay.buildermodel.AlipayTradeWapPayContentBuilder");
        //Loader::import('Alipay.wappay.service.AlipayTradeService');
        if (!empty($_POST['WIDout_trade_no'])&& trim($_POST['WIDout_trade_no'])!=""){
            //商户订单号，商户网站订单系统中唯一订单号，必填
            $out_trade_no = $_POST['WIDout_trade_no'];
            
            //订单名称，必填
            $subject = $_POST['WIDsubject'];

            //付款金额，必填
            $total_amount = $_POST['WIDtotal_amount'];

            //商品描述，可空
            $body = $_POST['WIDbody'];

            //超时时间
            $timeout_express="1m";
            include('../extend/AliPay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php');

            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);
            include('../extend/AliPay/wappay/service/AlipayTradeService.php');

            $payResponse = new \AlipayTradeService($config);
            $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
            return ;
        }
    }


    /**
     * [回调修改数据]
     *
     * @param Request
     */
    public function pay_code(Request $request){

        if($request->isGet()){
            $data['status'] = 2;
            if(!empty($_GET['out_trade_no'])){
                $shopping_goods = db("order")->where("order_information_number",$_GET["out_trade_no"])->field("goods_id,shopping_shop_id,order_num")->select();
                $goods = db("goods")->where("id",$shopping_goods[0]['goods_id'])->field("goods_num")->find();
                $goods_num = $goods['goods_num'] - 1;
                $seckill = db("seckill")->where("goods_id",$shopping_goods[0]['goods_id'])->find();
                if(empty($shopping_goods[0]["shopping_shop_id"])){
                    db("goods")->where("id",$shopping_goods[0]['goods_id'])->update(["goods_num"=>$goods_num]);
                }
                //第一次秒杀提交订单
                if(!empty($seckill["goods_num"])){
                    $seckill_num = $seckill["goods_num"] - 1;
                    db("seckill")->where("goods_id",$shopping_goods[0]['goods_id'])->update(["residue_num"=>$seckill_num]);
                }
                //购物车提交订单
                foreach ($shopping_goods as $key=>$value){
                    $goods_shopping_num = db("goods")->where("id",$value["goods_id"])->field("goods_num")->find();
                    if(!empty($value["shopping_shop_id"])){
                        $shopping_goods_num[] = $goods_shopping_num["goods_num"] - $value["order_num"];
                        db("goods")->where("id",$value["goods_id"])->update(["goods_num"=>$shopping_goods_num[$key]]);
                    }
                }

                $bool = db("order")->where("order_information_number",$_GET['out_trade_no'])->update($data);
                if($bool){
                    $this->redirect(url("index/index/index"));
                }
            }

        }
    }

}

