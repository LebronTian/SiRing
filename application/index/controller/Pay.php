<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/29
 * Time: 18:50
 */

namespace app\index\controller;
use think\Controller;
use think\Request;

class Pay extends Controller
{
    public function pay_order(Request $request)
    {
        //获取订单号
        $reoderSn = $request->only(['order'])['order'];

        //订单名称
        $order_name = $request->only(['order_name'])['order_name'];

        //获取支付金额
        $money = $request->only(['order_money'])['order_money'];
        //实例化alipay类
        $ali = new Alipay();

        //异步回调地址
        $url = 'http://localhost/SiRing/public/Alipay_pay_code';

        $array = $ali->alipay($order_name, $money,$reoderSn,  $url);

        if ($array) {
            return $array;
        } else {
            echo json_encode(array('status' => 0, 'msg' => '对不起请检查相关参数!@'));
        }

    }



    public function pay_code(){
       if ($_POST['trade_status'] == 'TRADE_SUCCESS'){
           echo "支付成功";
       }
    }
}
?>

