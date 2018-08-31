<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/29
 * Time: 19:24
 */

namespace app\index\controller;
use think\Controller;

class Callback extends Controller
{
    /*
     * 支付宝支付回调修改订单状态
     */
    public function aliPayBack()
    {

        if ($_POST['trade_status'] == 'TRADE_SUCCESS') {//如果支付成功
            //===============修改订单状态===========================//
            $orderSn = $_POST['out_trade_no'];//获取订单号
            $where['order_sn'] = $orderSn;
            echo $where;
            exit;
        }
    }
}
