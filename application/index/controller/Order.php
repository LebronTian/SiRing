<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/10
 * Time: 15:15
 */
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;


class Order extends Base {

    /**
     **************李火生*******************
     * @return \think\response\View
     * 订单
     **************************************
     */
    public function index(){
        $member =Session::get('member');
        if(!empty($member)){
            $member_information =Db::name('user')->field('harvester,harvester_phone_num,city,address')->where('phone_num',$member['phone_num'])->find();
            $user_id = db("user")->where('phone_num',$member['phone_num'])->field("id")->find();
        }
        $discounts_id = db("discounts_user")->where("user_id",$user_id['id'])->field("discounts_id")->find();
        $discounts = db("discounts")->where("id",$discounts_id['discounts_id'])->where('status',1)->find();
        if($discounts['status'] == 1){
            $this->assign("discounts",$discounts);
        }
       if(!empty($member_information['city'])){
           $my_position =explode(",",$member_information['city']);
           $position = $my_position[0].$my_position[1].$my_position[2].$member_information['address'];
       }
        if(!empty($my_position)){
            $this->assign('member_information',$member_information);
        }
        if(!empty($position)){
            $this->assign('position',$position);
        }
        //直接从买入过来
        $commodity_id =Session::get('goods_id');
        $shopping_id =Session::get('shopping');
        if(!empty($commodity_id)&&empty($shopping_id)){
            session('shopping',null);
            $datas =Db::name('goods')->where('id',$commodity_id)->find();

            $express_fee =0.00;
            /*促销*/
            $seckill_money =Db::name('seckill')->field('seckill_money')->where('goods_id',$commodity_id)->find();
            if(!empty($seckill_money)){
                $goods_bottom_money =$seckill_money['seckill_money'];
                $all_money = $goods_bottom_money + $express_fee- $discounts['discounts_money'];
            }
            /*正常流程*/
            if(empty($seckill_money)){
                $goods_bottom_money=$datas['goods_bottom_money'];
                $all_money = $goods_bottom_money + $express_fee - (float)$discounts['discounts_money'];
            }
            /*总费用*/
            $data =[
                'commodity_id'=>$commodity_id,
                'goods_name'=>$datas['goods_name'],
                'goods_bottom_money'=>$goods_bottom_money,
                'goods_show_images'=>$datas['goods_show_images'],
                //运费
                'express_fee'=>$express_fee,
                //总计
                'all_money'=>$all_money
            ];
            $this->assign('data',$data);
        };
        //从购物车过来
//        $shopping_id =Session::get('shopping');
        if(!empty($shopping_id)){
            session('goods_id',null);
           $shopping =Db::name('shopping_shop')->where('id',$shopping_id['id'])->find();
           $shop_id =explode(',',$shopping['shopping_id']);
            if(is_array($shop_id)){
                $where ='id in('.implode(',',$shop_id).')';
            }else{
                $where ='id='.$shop_id;
            }
            $list =  Db::name('shopping')->where($where)->select();
            $this->assign('list',$list);
            $this->assign('all_money',$shopping['money']);
        }
        return view("index");
    }

    /**
     **************李火生*******************
     * ios提交订单传过来的参数形成订单存库并返回对应的订单号给IOS
     * 'goods_name':goods_name, //商品名字
    'order_num':order_num,      //商品数量
    'all_pay':all_pay,             //实付金额
    'express_fee':express_fee,      //快递费
    'unit_price': unit_price        //商品的价格
     'goods_id':                    //商品的Id
     * position 地址
     **************************************
     */
    public function  ios_api_order_button(Request $request){
        if ($request->isPost()) {
            $data = $_POST;
            $member_data = session('member');
            $member = Db::name('user')->field('id,harvester,harvester_phone_num,city,address')->where('phone_num', $member_data['phone_num'])->find();

            if (empty($member['harvester']) || empty($member['harvester_phone_num']) || empty($member['city']) || empty($member['address'])) {
                return ajax_error('请填写收货人信息',['status'=>0]);
            }
            if (!empty($member['city'])) {
                $my_position = explode(",", $member['city']);
                $position = $my_position[0] . $my_position[1] . $my_position[2] . $member['address'];
            }else{
                return ajax_error('请填写收货地址',['status'=>0]);
            }
//            $position =$_POST['position'];
            //从点击买入一步步过来
            $commodity_id = $_POST['goods_id'];
            if (!empty($commodity_id)) {
                Session::delete('shopping');
                $goods_data = Db::name('goods')->where('id', $commodity_id)->find();
                $create_time = time();
                if (!empty($data)) {
                    $datas = [
                        'goods_img' => $goods_data['goods_show_images'],
                        'goods_name' => $data['goods_name'],
                        'order_num' => $data['order_num'],
                        'user_id' => $member['id'],
                        'harvester' => $member['harvester'],
                        'harvest_phone_num' => $member['harvester_phone_num'],
                        'harvest_address' => $position,
                        'create_time' => $create_time,
                        'pay_money' => $data['all_pay'],
                        'status' => 1,
                        'goods_id' => $commodity_id,
                        'send_money' => $data['express_fee'],
                        'order_information_number' => $create_time . $member['id'],//时间戳+用户id构成订单号
                    ];
                    $res = Db::name('order')->insertGetId($datas);
                    if ($res) {
                        //TODO:
//                        Session::delete('goods_id');
                        session('order_id', $res);
                        $discounts =  Db::name('discounts_user')->field('discounts_id')->where('user_id',$member['id'])->find();
                        if(!empty($discounts)){
                            $bools =Db::name('discounts')->where('id',$discounts['discounts_id'])->update(['status'=>2]);
                        }
                        return ajax_success('下单成功', $datas['order_information_number']);
                    }
                }
            }
            //从购物车过来的

            $shopping_id = Session::get('shopping');
            if (!empty($shopping_id)) {
                //TODO:
//                Session::delete('goods_id');
                $shopping = Db::name('shopping_shop')->where('id', $shopping_id['id'])->find();
                $shop_id = explode(',', $shopping['shopping_id']);
                if (is_array($shop_id)) {
                    $where = 'id in(' . implode(',', $shop_id) . ')';
                } else {
                    $where = 'id=' . $shop_id;
                }
                $list = Db::name('shopping')->where($where)->select();
                $create_time = time();
                foreach ($list as $k => $v) {
                    if (!empty($data)) {
                        $datas = [
                            'goods_img' => $v['goods_images'],
                            'goods_name' => $data['goods_name'][$k],
                            'order_num' => $data['order_num'][$k],
                            'user_id' => $member['id'],
                            'harvester' => $member['harvester'],
                            'harvest_phone_num' => $member['harvester_phone_num'],
                            'harvest_address' => $position,
                            'create_time' => $create_time,
                            'pay_money' => $data['all_pay'],
//                            'pay_money' => $v['money'],
                            'status' => 1,
                            'goods_id' => $v['goods_id'],
                            'send_money' => $data['express_fee'],
                            'order_information_number' => $create_time . $member['id'],//时间戳+用户id构成订单号
                            'shopping_shop_id' => $v['id']
                        ];
                        $res =Db::name('order')->insertGetId($datas);
//                        session('order_id', $res);
                        /*下单成功对购物车里面对应的商品进行删除*/
                    }

                }
                if (!empty($res)) {
                    Session::delete('shopping');
                    Db::name('shopping')->where($where)->delete();
                    Db::name('shopping_shop')->where('id',$shopping_id['id'])->delete();
                    return ajax_success('下单成功', $datas);
                }


            }
        }
    }

    /**
     **************李火生*******************
     * ios购物车提交订单传过来的参数形成订单存库并返回对应的订单号给IOS
     * 'goods_name':goods_name, //商品名字
    'order_num':order_num,      //商品数量
    'all_pay':all_pay,             //实付金额
    'express_fee':express_fee,      //快递费
    'unit_price': unit_price        //商品的价格
    'shopping_id':                    //购物车的Id
     * 'money'
     *
     **************************************
     */
    public function  ios_api_order_button_by_shop(Request $request){
        if ($request->isPost()) {
            $member_data = session('member');
            $member = Db::name('user')->field('id,harvester,harvester_phone_num,city,address')->where('phone_num', $member_data['phone_num'])->find();
            if (empty($member['harvester']) || empty($member['harvester_phone_num']) || empty($member['city']) || empty($member['address'])) {
                return ajax_error('请填写收货人信息',['status'=>0]);
            }
            if (!empty($member['city'])) {
                $my_position = explode(",", $member['city']);
                $position = $my_position[0] . $my_position[1] . $my_position[2] . $member['address'];
            }else{
                return ajax_error('请填写收货地址',['status'=>0]);
            }
            //从购物车过来的
            $shopping_id = $_POST['shopping_id'];

            if (!empty($shopping_id)) {
                $shopping = Db::name('shopping_shop')->where('id', $shopping_id)->find();
                if(!empty($shopping)){
                    $shop_id = explode(',', $shopping['shopping_id']);
                    if (is_array($shop_id)) {
                        $where = 'id in(' . implode(',', $shop_id) . ')';
                    } else {
                        $where = 'id=' . $shop_id;
                    }
                         $list = Db::name('shopping')->where($where)->select();
                        if(!empty($list)){
                                $create_time = time();
                                foreach ($list as $k => $v) {
                                $data = $_POST;
                                    $datas = [
                                        'goods_img' => $v['goods_images'],
                                        'goods_name' => $data['goods_name'][$k],
                                        'order_num' => $data['order_num'][$k],
                                        'user_id' => $member['id'],
                                        'harvester' => $member['harvester'],
                                        'harvest_phone_num' => $member['harvester_phone_num'],
                                        'harvest_address' => $position,
                                        'create_time' => $create_time,
                                        'pay_money' => $data['all_pay'],
                                        'status' => 1,
                                        'goods_id' => $v['goods_id'],
                                        'send_money' => $data['express_fee'],
                                        'order_information_number' => $create_time . $member['id'],//时间戳+用户id构成订单号
                                        'shopping_shop_id' => $v['id']
                                    ];
                                    $res =Db::name('order')->insertGetId($datas);
                                    /*下单成功对购物车里面对应的商品进行删除*/
                                }
                                if($res){
                                    $order_information_numbers =Db::name('order')->field('order_information_number')->where('id',$res)->find();
                                    $res_one = Db::name('shopping')->where($where)->delete();
                                    if($res_one){
                                        $res_tow = Db::name('shopping_shop')->where('id',$shopping_id)->delete();
                                        if($res_tow){
                                            return ajax_success('下单成功',$order_information_numbers['order_information_number']);
                                        }else{
                                            return ajax_success('下单成功',2);
                                        }
                                    }else{
                                        return ajax_success('下单成功', 3);
                                    }
                                }else{
                                    return ajax_success('下单失败',['status'=>0]);
                                }

                            }else{
                                return ajax_error('错误',['status'=>0]);
                            }
                    }else{
                        return ajax_error('没有数据返回',['status'=>0]);
                    }
                }else{
                    return ajax_error('没有数据返回',['status'=>0]);
                }

           }

    }

    /**
     **************李火生*******************
     * 生成支付宝签名 TODO:支付宝签名
     **************************************
     */
    public function ios_api_alipay(Request $request){
        if($request->isPost()){
            $order_num =$request->only(['order_num'])['order_num'];
            $product_code ="QUICK_MSECURITY_PAY";
            $out_trade_no="ZQLM3O56MJD4SK3";
            $time =date('Y-m-d H:i:s');
            if(!empty( $order_num)){
                $counts =Db::name('order')->where('order_information_number',$order_num)->count();
                if($counts==1){
                    $data = Db::name('order')->where('order_information_number',$order_num)->select();
                    if(!empty($data)){
                        foreach ($data as $k=>$v){
                            $goods_name = $v['goods_name'];
                            $order_num = $v['order_information_number'];
                            $goods_pay_money =$v['pay_money'];
                            $subject =$v['order_num'];
                            $app_id ="{'timeout_express':'30m','seller_id':"."'".$order_num."'".",'product_code':"."'".$product_code."'".",'total_amount':"."'".$goods_pay_money."'".",'subject':"."'".$subject."'".",'body':"."'".$goods_name."'".",'out_trade_no':"."'".$out_trade_no."'"."}";
                            $app_ids =urlencode($app_id);
                            $time_encode =urlencode($time);
                            // 订单信息，在iOS端加密
                            include('../vendor/Alipays/aop/AopClient.php');
                            $private_path="MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==";
                            //构造业务请求参数的集合(订单信息)
                            $content = array();
                            $content['subject'] = $goods_name;
                            $content['out_trade_no'] = $order_num;
                            $content['timeout_express'] = "90m";
                            $content['total_amount'] = $goods_pay_money;
                            $content['product_code'] = "QUICK_MSECURITY_PAY";
                            $con = json_encode($content);//$content是biz_content的值,将之转化成json字符串
                            //公共参数
                            $Client = new \AopClient();//实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
                            $param['app_id'] = '2018082761132725';
                            $param['method'] = 'alipay.trade.app.pay';//接口名称，固定值
                            $param['charset'] = 'utf-8';//请求使用的编码格式
                            $param['sign_type'] = 'RSA2';//商户生成签名字符串所使用的签名算法类型
                            $param['timestamp'] = date("Y-m-d H:i:s");//发送请求的时间
                            $param['version'] = '1.0';//调用的接口版本，固定为：1.0
                            $param['notify_url'] = 'https://vip.gagaliang.com/notifyurl';
                            $param['biz_content'] = $con;//业务请求参数的集合,长度不限,json格式，即前面一步得到的
                            $paramStr = $Client->getSignContent($param);//组装请求签名参数
                            $sign = $Client->alonersaSign($paramStr, $private_path, 'RSA2', false);//生成签名()
                            $param['sign'] = $sign;
                            $str = $Client->getSignContentUrlencode($param);//最终请求参数
                        }
                        return ajax_success('数据成功返回',$str);
                    }else{
                        return ajax_error('数据返回不成功',['status'=>0]);
                    }
                }else if($counts>1){
                    $data = Db::name('order')->where('order_information_number',$order_num)->select();
                    if(!empty($data)){
                        foreach ($data as $k=>$v){
                            $goods_name = $v['goods_name'];
                            $order_num = $v['order_information_number'];
                            $goods_pay_money =$v['pay_money'];
                            $subject =$v['order_num'];
                            $app_id ="{'timeout_express':'30m','seller_id':"."'".$order_num."'".",'product_code':"."'".$product_code."'".",'total_amount':"."'".$goods_pay_money."'".",'subject':"."'".$subject."'".",'body':"."'".$goods_name."'".",'out_trade_no':"."'".$out_trade_no."'"."}";
                            $app_ids =urlencode($app_id);
                            $time_encode =urlencode($time);
                            // 订单信息，在iOS端加密
                            include('../vendor/Alipays/aop/AopClient.php');
                            $private_path="MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==";
                            //构造业务请求参数的集合(订单信息)
                            $content = array();
                            $content['subject'] = $goods_name;
                            $content['out_trade_no'] = $order_num;
                            $content['timeout_express'] = "90m";
                            $content['total_amount'] = $goods_pay_money;
                            $content['product_code'] = "QUICK_MSECURITY_PAY";
                            $con = json_encode($content);//$content是biz_content的值,将之转化成json字符串
                            //公共参数
                            $Client = new \AopClient();//实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
                            $param['app_id'] = '2018082761132725';
                            $param['method'] = 'alipay.trade.app.pay';//接口名称，固定值
                            $param['charset'] = 'utf-8';//请求使用的编码格式
                            $param['sign_type'] = 'RSA2';//商户生成签名字符串所使用的签名算法类型
                            $param['timestamp'] = date("Y-m-d H:i:s");//发送请求的时间
                            $param['version'] = '1.0';//调用的接口版本，固定为：1.0
                            $param['notify_url'] = 'https://vip.gagaliang.com/notifyurl';
                            $param['biz_content'] = $con;//业务请求参数的集合,长度不限,json格式，即前面一步得到的
                            $paramStr = $Client->getSignContent($param);//组装请求签名参数
                            $sign = $Client->alonersaSign($paramStr, $private_path, 'RSA2', false);//生成签名()
                            $param['sign'] = $sign;
                            $str = $Client->getSignContentUrlencode($param);//最终请求参数
                            return ajax_success('数据成功返回',$str);
                        }

                    }else{
                        return ajax_error('数据返回不成功',['status'=>0]);
                    }
                }else{
                    return ajax_error('没有这个订单号',['status'=>0]);
                }
            }else{
                return ajax_error('失败',['status'=>0]);
            }
        }
    }

    /**
     **************李火生*******************
     * 异步处理(支付宝IOS对接)
     **************************************
     */
    public function notifyurl(Request $request)
    {
        //这里可以做一下你自己的订单逻辑处理
        $pay_time = time();
        $data['pay_time'] = $pay_time;
        //原始订单号
        $out_trade_no = $request->param('out_trade_no');
        //支付宝交易号
        $trade_no = input('trade_no');
        //交易状态
        $trade_status =  $request->param('trade_status');
        if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
            $data['status'] = 2;
            return ajax_success('测试',$data);
            $condition['order_information_number'] = $out_trade_no;
            $select_data =Db::name('order')->where($condition)->select();
            foreach ($select_data as $key=>$val){
                $result = Db::name('order')->where($condition)->update($data);//修改订单状态,支付宝单号到数据库
            }
            if ($result) {
                return ajax_success('支付成功', ['status' =>1]);
            } else {
                return ajax_error('验证失败',['status'=>0]);
            }
        } else {
            return ajax_error('验证失败',['status'=>0]);
        }
    }




//    public function notifyurl()
//    {
////        $aop = new \AopClient;
////            $aop->alipayrsaPublicKey = "MIIEpAIBAAKCAQEA1YHhYsj9AaXi+UvoMWp8EqiJHHqD+O+YbQ9nF8POQuyaTPW4hZZPIq/HoEBIyu8Myh7UT5DS5HFA4ZUZeeySTISCPFUWbppQf7YM4UXuzqSzERQmcFw4wpfQglfb6ECuGyH81C7ibhHvoibzeFESMCC1nHvmMl+AjEqBK7b/CCJpC4KwPzoloivFNy8rXfP/mSocbADSfAORhJo15fjbQT2ixy7mRUA8bIK2hBLbDp8JsStJ3BoLGS4/zX0jQJRXQfeE3DLt95ITIul4RMTFnZ151fS7ylOgNWeAacDQ3fet7IF54QWP5zW9M7j8gcy7UexJdMfffPoZvvSetIItFwIDAQABAoIBAQCkJsZ1n9e985+NUgoELD2WTtOT/LIIq5WCjCwT/mxP0f9UGjuzIXxYS9NsZuBQffhUUd2kCtHJ5zUd+vdqYTOd9ub2oeisQqKPfhVrAcx4PfKat+ZRzuWo3vXlsM0XRNtXawsqy501ST73aYEZSSN1s0BOPogexIRd2E51oK11vy+BBotPOXmSj6sKAFNJ6drD3ckdJ678s+mYTxdIoKi45l+5wwpHxL7qsPTeuBijhhBufY6KzkxRYlR+zq5M+pVJcnsh3TZWsbW6z73tb/C31E5cOnuy2l53YHe20dda0Om6w41+QhekuZvwZyLWYqXeLYCc2tfR1DHs2Zicv6ZJAoGBAO1D0eZIldte7Ka6Yd+zrNhaNvL8JGUSosGmKwdS6PHQA27IBuVd";
////            $aop->alipayrsaPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQAB";
////            $aop->alipayrsaPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmKw/C4jHlGLUhpATv2yesGaZOSI9MmOuw5AMcB6odktNj19CSNDAmS5gDCKM4bJyVFOCFb3BNgvADvhoXHMPngGUkqHkJuRotGpvbr3A5UCyWLsF442cFnO7KZC5blKY59DmB/zZ7E9gRT5BhmQebJkkMls2PcVkvEUNTdQiorcNunhxOfsyUuYqsZP0yPoptR8YarmiWZVXwNxJ7Ha3zVzc7kVPqNYyDkCYtSfvVjOeutUh2dGsz1irsYUZpQP4Kra2YyhPlXpNS/KR3TSl1eLXxAQH6g1YWIsQ7/AZRi+Qv1mczwB9miYjQyPkEtjyYQkHVaItxeGW3fvsSvXy9QIDAQAB";
////
////        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
////        if($flag) {
//            //验证成功
//            //这里可以做一下你自己的订单逻辑处理
//            $data =['status'=>2];
//            $pay_time = time();
//            $data['pay_time'] = $pay_time;
//            //原始订单号
//            $out_trade_no = input('out_trade_no');
//            //支付宝交易号
//            $trade_no = input('trade_no');
//            //交易状态
//            $trade_status = input('trade_status');
//
//            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
//                $condition['order_information_number'] = $out_trade_no;
////                $data['status'] = 2;
////                $data['third_ordersn'] = $trade_no;
////                $result = Db::name('order')->where($condition)->update($data);//修改订单状态,支付宝单号到数据库
//                $result = Db::name('order')->where('order_information_number',$out_trade_no)->update($data);//修改订单状态,支付宝单号到数据库
//                if ($result) {
//                    echo 'success';//这个必须返回给支付宝，响应个支付宝
////                    return ajax_success('支付成功', ['statuss' => 1]);
//                } else {
//                    echo 'error';
////                    return ajax_error('支付失败', ['statuss' => 0]);
//                }
//            } else {
//                echo 'error';
////                return ajax_error('支付失败', ['statuss' => 0]);
//            }
////        }
//
////                if(!empty($_GET['out_trade_no'])){
////                    $shopping_goods = db("order")->where("order_information_number",$_GET["out_trade_no"])->field("goods_id,shopping_shop_id,order_num")->select();
////                    $goods = db("goods")->where("id",$shopping_goods[0]['goods_id'])->field("goods_num")->find();
////                    $goods_num = $goods['goods_num'] - 1;
////                    $seckill = db("seckill")->where("goods_id",$shopping_goods[0]['goods_id'])->find();
////                    if(empty($shopping_goods[0]["shopping_shop_id"])){
////                        db("goods")->where("id",$shopping_goods[0]['goods_id'])->update(["goods_num"=>$goods_num]);
////                    }
////                    //第一次秒杀提交订单
////                    if(!empty($seckill["goods_num"])){
////                        $seckill_num = $seckill["goods_num"] - 1;
////                        db("seckill")->where("goods_id",$shopping_goods[0]['goods_id'])->update(["residue_num"=>$seckill_num]);
////                    }
////                    //购物车提交订单
////                    foreach ($shopping_goods as $key=>$value){
////                        $goods_shopping_num = db("goods")->where("id",$value["goods_id"])->field("goods_num")->find();
////                        if(!empty($value["shopping_shop_id"])){
////                            $shopping_goods_num[] = $goods_shopping_num["goods_num"] - $value["order_num"];
////                            db("goods")->where("id",$value["goods_id"])->update(["goods_num"=>$shopping_goods_num[$key]]);
////                        }
////                    }
////                    $bool = db("order")->where("order_information_number",$_GET['out_trade_no'])->update($data);
////                    if($bool){
////                        $this->redirect(url("index/index/index"));
////                    }
////                }
//
//
////            echo 'success';//这个必须返回给支付宝，响应个支付宝，
////        } else {
////            //验证失败
////          return ajax_error('验证失败',['status'=>0]);
////        }
//        //$flag返回是的布尔值，true或者false,可以根据这个判断是否支付成功
//    }



    /**
     **************李火生*******************
     * @param Request $request
     * 提交订单
     **************************************
     */
    public function  bt_order(Request $request)
    {
        if ($request->isPost()) {
            $data = $_POST;
            $member_data = session('member');
            $member = Db::name('user')->field('id,harvester,harvester_phone_num,city,address')->where('phone_num', $member_data['phone_num'])->find();
            if (empty($member['harvester']) || empty($member['harvester_phone_num']) || empty($member['city']) || empty($member['address'])) {
                $this->error('请填写收货人信息');
            }
            if (!empty($member['city'])) {
                $my_position = explode(",", $member['city']);
                $position = $my_position[0] . $my_position[1] . $my_position[2] . $member['address'];
            }
            //从点击买入一步步过来
            $commodity_id = Session::get('goods_id');
            if (!empty($commodity_id)) {
                Session::delete('shopping');
                $goods_data = Db::name('goods')->where('id', $commodity_id)->find();
                $create_time = time();
                if (!empty($data)) {
                    $datas = [
                        'goods_img' => $goods_data['goods_show_images'],
                        'goods_name' => $data['goods_name'][0],
                        'order_num' => $data['order_num'][0],
                        'user_id' => $member['id'],
                        'harvester' => $member['harvester'],
                        'harvest_phone_num' => $member['harvester_phone_num'],
                        'harvest_address' => $position,
                        'create_time' => $create_time,
                        'pay_money' => $data['all_pay'],
                        'status' => 1,
                        'goods_id' => $commodity_id,
                        'send_money' => $data['express_fee'],
                        'order_information_number' => $create_time . $member['id'],//时间戳+用户id构成订单号
                    ];
                    $res = Db::name('order')->insertGetId($datas);
                    if ($res) {
                        //TODO:
//                        Session::delete('goods_id');
                        session('order_id', $res);
                        $discounts =  Db::name('discounts_user')->field('discounts_id')->where('user_id',$member['id'])->find();
                        if(!empty($discounts)){
                            $bools =Db::name('discounts')->where('id',$discounts['discounts_id'])->update(['status'=>2]);
                        }

                        return ajax_success('下单成功', $datas);
                    }
                }
            }
            //从购物车过来的
            $shopping_id = Session::get('shopping');
            if (!empty($shopping_id)) {
                //TODO:
//                Session::delete('goods_id');
                $shopping = Db::name('shopping_shop')->where('id', $shopping_id['id'])->find();
                $shop_id = explode(',', $shopping['shopping_id']);
                if (is_array($shop_id)) {
                    $where = 'id in(' . implode(',', $shop_id) . ')';
                } else {
                    $where = 'id=' . $shop_id;
                }
                $list = Db::name('shopping')->where($where)->select();
                $create_time = time();
                foreach ($list as $k => $v) {
                    if (!empty($data)) {
                        $datas = [
                            'goods_img' => $v['goods_images'],
                            'goods_name' => $data['goods_name'][$k],
                            'order_num' => $data['order_num'][$k],
                            'user_id' => $member['id'],
                            'harvester' => $member['harvester'],
                            'harvest_phone_num' => $member['harvester_phone_num'],
                            'harvest_address' => $position,
                            'create_time' => $create_time,
//                            'pay_money' => $data['all_pay'],
                            'pay_money' => $v['money'],
                            'status' => 1,
                            'goods_id' => $v['goods_id'],
                            'send_money' => $data['express_fee'],
                            'order_information_number' => $create_time . $member['id'],//时间戳+用户id构成订单号
                            'shopping_shop_id' => $v['id']
                        ];
                        $res =Db::name('order')->insertGetId($datas);
//                        session('order_id', $res);
                        /*下单成功对购物车里面对应的商品进行删除*/

                    }

                }
                if ($res) {
                    Session::delete('shopping');
                    Db::name('shopping')->where($where)->delete();
                    Db::name('shopping_shop')->where('id',$shopping_id['id'])->delete();
                    return ajax_success('下单成功', $datas);
                }


            }
        }
    }

    /**
     **************李火生*******************
     * 购买商品时候需要绑定的用户id
     **************************************
     */
        public function  common_id(Request $request){

            if($request->isPost()){
                $data =session('member');
                    $member_id =Db::name('user')->field('id')->where('phone_num',$data['phone_num'])->find();
                if (empty($data)){
                    $this->redirect('index/Login/login');
                }
                if(!empty($data)){
                    return  ajax_success('成功',$member_id);
                }
            }

    }


    /**
     **************李火生*******************
     * @return \think\response\View
     * 订单详情页面
     **************************************
     */
        public function details(){
            /*判断来自于购物订单列表*/
            $order_from_shop_id = Session::get("save_order_information_number");
            if(!empty($order_from_shop_id)){
                session('order_id_from_myorder',null);
                /*先通过查找订单编号*/
//                $order_information_id =Db::name('order')->field('order_information_number')->where('id',$order_from_shop_id)->find();
//                $order_id =$order_information_id['order_information_number'];
                $order_id =$order_from_shop_id;
                if(!empty($order_id)){
                    /*先清除之前的*/
                    $data=Db::table("tb_order")
                        ->field("tb_order.*,tb_goods.goods_bottom_money goods_bottom_money")
                        ->join("tb_goods","tb_order.goods_id=tb_goods.id",'left')
                        ->where('tb_order.order_information_number',$order_id)
                        ->select();
                    $datas =Db::name('order')->where('order_information_number',$order_id)->find();
                    $this->assign('data',$data);
                    $this->assign('datas',$datas);
                    session('save_order_information_number',null);
                }
            }
            /*判断来自于我的订单列表点击订单详情*/
            $order_from_myorder_bt =Session::get('order_id_from_myorder');
            if(!empty($order_from_myorder_bt)){
                $order_information_id =Db::name('order')->field('order_information_number')->where('id',$order_from_myorder_bt)->find();
                $order_id =$order_information_id['order_information_number'];
                    $data=Db::table("tb_order")
                        ->field("tb_order.*,tb_goods.goods_bottom_money goods_bottom_money")
                        ->join("tb_goods","tb_order.goods_id=tb_goods.id",'left')
                        ->where('tb_order.order_information_number',$order_id)
                        ->select();
                $datas =Db::name('order')->where('order_information_number',$order_id)->find();
                $this->assign('data',$data);
                $this->assign('datas',$datas);
                    session('order_id_from_myorder',null);
            }
            return view('details');
        }

    /**
     **************李火生*******************
     * 订单详情页的取消按钮
     **************************************
     */
        public function order_detail_del(Request $request){
            if($request->isPost()){
                $order_information_number =$request->only(['order_detail_del'])['order_detail_del'];
                if(!empty($order_information_number)){
                    $res =Db::name('order')->where('order_information_number',$order_information_number)->update(['status'=>11]);
                    if($res){
                        $this->success('订单取消成功');
                    }else{
                        $this->error('订单取消失败');
                    }
                }
            }
        }

    /**
     **************李火生*******************
     * @param Request $request
     **************************************
     */
        public function save_order_information_number(Request $request){
            if($request->isPost()){
                $save_order_information_number =$request->only(['order_informartion_number'])['order_informartion_number'];
                if(!empty($save_order_information_number)){
                    session('save_order_information_number',$save_order_information_number);
                    return ajax_success('成功');
                }
            }
        }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 我的订单
     **************************************
     */
    public function ajax_id(Request $request){
        if($request->isPost()){
            $id = $request->only(["order_id"])['order_id'];
            Session("order_id_from_myorder",$id);
            return ajax_success("获取成功",$id);
        }
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 我的订单显示
     **************************************
     */
        public function myorder(){
            $datas =session('member');
            $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
            $data =Db::name('order')->where('user_id',$member_id['id'])->order('create_time','desc')->select();
            $this->assign('data',$data);
            return view('myorder');
        }

    /**
     **************李火生*******************
     * @param Request $request
     * TODO：所有订单IOs接口数据返回
     **************************************
     */
        public function ios_api_myorder(Request $request){
            if($request->isPost()){
                $datas =session('member');
                if(!empty($datas)){
                    $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
                    if(!empty($datas)){
                        $data =Db::name('order')->where('user_id',$member_id['id'])->order('create_time','desc')->select();
                        if(!empty($data)){
                            return ajax_success('全部信息返回成功',$data);
                        }
                    }
                }else{
                   return ajax_error('请登录',['status'=>0]);
                }
            }
        }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 待支付
     **************************************
     */
        public function wait_pay(){
            $datas =session('member');
            $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
            $data =Db::name('order')->where('status',1)->where('user_id',$member_id['id'])->order('create_time','desc')->select();
            $this->assign('data',$data);
            return view('wait_pay');
        }

    /**
     **************李火生*******************
     * @return \think\response\View
     * TODO:待支付IOS数据返回
     **************************************
     */
        public function ios_order_wait_pay(){
            $datas =session('member');
            $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
           if(!empty($member_id)){
               $data =Db::name('order')->where('status',1)->where('user_id',$member_id['id'])->order('create_time','desc')->select();
               if(!empty($data)){
                   return ajax_success('待支付数据返回成功',$data);
               }else{
                   return ajax_error('暂无待支付',['status'=>0]);
               }
           }else{
               return ajax_error('请登录',['status'=>0]);
           }

        }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 代发货
     **************************************
     */
        public function wait_deliver(){
            $datas =session('member');
            $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
            $data =Db::name('order')->where('status',2)->where('user_id',$member_id['id'])->order('create_time','desc')->select();
            $this->assign('data',$data);
            return view('wait_deliver');
        }

    /**
     **************李火生*******************
     * TODO:IOS接口待发货
     **************************************
     */
        public function ios_order_wait_deliver(){
            $datas =session('member');
            $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
            if(!empty($member_id)){
                $data =Db::name('order')->where('status',2)->where('user_id',$member_id['id'])->order('create_time','desc')->select();
                if(!empty($data)){
                    return ajax_success('待发IOS接口数据返回成功',$data);
                }else{
                    return ajax_error('暂无待发货订单',['status'=>0]);
                }
            }else{
                return ajax_error('请登录',['status'=>0]);
            }

        }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 待收货
     **************************************
     */
        public function take_deliver(){
            $datas =session('member');
            $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
            $data =Db::name('order')
                ->where("status=3 or status=4")
                ->where('user_id',$member_id['id'])
                ->order('create_time','desc')
                ->select();
            $this->assign('data',$data);
            return view('take_deliver');
        }

    /**
     **************李火生*******************
     * TODO：IOS待收货接口返回
     **************************************
     */
        public function  ios_order_take_deliver(Request $request){
            if($request->isPost()){
                $datas =session('member');
                $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
                if(!empty($member_id)){
                    $data =Db::name('order')
                        ->where("status=3 or status=4")
                        ->where('user_id',$member_id['id'])
                        ->order('create_time','desc')
                        ->select();
                    if(!empty($data)){
                        return ajax_success('待收货ios数据返回成功',$data);
                    }else{
                        return ajax_error('暂无待收货',['status'=>0]);
                    }
                }else{
                    return ajax_error('请登录',['status'=>0]);
                }
            }
        }
    /**
     **************李火生*******************
     * @return \think\response\View
     * 待评价
     **************************************
     */
        public function evaluate(){
            $datas =session('member');
            $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
            $data =Db::name('order')
                ->where("status=5 or status=6")
                ->where('user_id',$member_id['id'])
                ->order('create_time','desc')
                ->select();
            $this->assign('data',$data);
            return view('evaluate');
        }

    /**
     **************李火生*******************
     * TODO:iso与待评价的接口
     **************************************
     */
        public function ios_order_evaluate(){
            $datas =session('member');
            $member_id =Db::name('user')->field('id')->where('phone_num',$datas['phone_num'])->find();
           if(!empty($member_id)){
               $data =Db::name('order')
                   ->where("status=5 or status=6")
                   ->where('user_id',$member_id['id'])
                   ->order('create_time','desc')
                   ->select();
               if(!empty($data)){
                   return ajax_success('iso与待评价的接口数据返回成功',$data);
               }else{
                  return ajax_error('暂无待评价',['status'=>0]);
               }
           }else{
               return ajax_error('请登录',['status'=>0]);
           }
        }


    /**
     **************李火生*******************
     * @param Request $request
     * 前端点击取消订单通过ajax发送一个order_id取消订单
     **************************************
     */
        public function cancel_order(Request $request){
            if($request->isPost()){
                $order_id =$_POST['order_id'];
                if(!empty($order_id)){
                    $res =Db::name('order')->where('id',$order_id)->update(['status'=>11]);
                    if($res){
                        $this->success('订单取消成功');
                    }else{
                        $this->error('订单取消失败');
                    }
                }
            }
        }



    /**
     **************李火生*******************
     * @param Request $request
     * TODO:IOS对接取消订单
     **************************************
     */
    public function ios_api_cancel_order(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            if(!empty($order_id)){
                $res =Db::name('order')->where('id',$order_id)->update(['status'=>11]);
                if($res){
                   return ajax_success('订单取消成功',['status'=>1]);
                }else{
                    return ajax_error('订单取消失败',['status'=>0]);
                }
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * TODO：IOS接口返回（删除订单）
     **************************************
     */
    public function ios_api_delete_order(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            if(!empty($order_id)){
                $res =Db::name('order')->where('id',$order_id)->delete();
                if($res){
                    return ajax_success('订单删除成功',['status'=>1]);
                }else{
                    return ajax_error('订单删除失败',['status'=>0]);
                }
            }
        }
    }



    /**
     **************李火生*******************
     * @param Request $request
     * 买家确认收货
     **************************************
     */
        public function collect_goods(Request $request){
            if ($request->isPost()){
                $order_id =$_POST['order_id'];
                if(!empty($order_id)){
                    $res =Db::name('order')->where('id',$order_id)->update(['status'=>5]);
                    if($res){
                       $this->success('确认收货成功',url('take_deliver'));
                    }else{
                        $this->error('确认收货失败');
                    }
                }
            }
        }


    /**
     **************李火生*******************
     * @param Request $request
     * TODO:IOS接口（买家确认收货）
     **************************************
     */
    public function ios_api_collect_goods(Request $request){
        if ($request->isPost()){
            $order_id =$_POST['order_id'];
            if(!empty($order_id)){
                $res =Db::name('order')->where('id',$order_id)->update(['status'=>5]);
                if($res){
                    return ajax_success('确认收货成功',['status'=>1]);
                }else{
                   return ajax_error('确认收货失败',['status'=>0]);
                }
            }else{
                return ajax_error('没有这个订单',['status'=>0]);
            }
        }
    }

    /**
     * 实时物流显示
     */
    public function logistics_information(Request $request){
        if ($request->isPost()) {
            $order_id =$_POST['order_id'];
            session('by_order_id',$order_id);
            if(!empty($order_id)){
              $this->success('成功','index/Order/logistics_information');
            }
        }
            return view('logistics_information');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * TODO：IOS对接实时物流显示
     **************************************
     */
    public function ios_api_logistics_information(Request $request){
        if ($request->isPost()) {
            $order_id =$_POST['order_id'];
            session('by_order_id',$order_id);
            if(!empty($order_id)){
               return ajax_success('查看物流信息成功',['status'=>1]);
            }else{
                return ajax_error('没有这个订单号',['status'=>0]);
            }
        }
    }


    /**
     **************李火生*******************
     * 待收货查看物流传的order_Id
     **************************************
     */
    public  function logistics_information_id(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            session('by_order_id',$order_id);
            if(!empty($order_id)){
                $this->success('成功','index/Order/logistics_information');
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * TODO:IOS对接待收货查看物流传的order_Id
     **************************************
     */
    public  function ios_api_logistics_information_id(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            session('by_order_id',$order_id);
            if(!empty($order_id)){
                return ajax_success('待收货查看物流获取id成功',['status'=>1]);
            }else{
                return ajax_error('订单不存在',['status'=>0]);
            }
        }
    }




    /**
     **************李火生*******************
     * 快递100接口
     **************************************
     */
    public function interface_information(Request $request)
    {
        if ($request->isPost()) {
            $order_id =Session::get('by_order_id');
            if(!empty($order_id)) {
                $express =Db::name('order')->field('express_num,express_type')->where('id',$order_id)->find();
                if(!empty($express)){
                    $express_type =$express['express_type'];
                    $express_num =$express['express_num'];
                    if($express_type =="顺丰"){
                        $express_types ="shunfeng";
                    }
                    if($express_type=="EMS"){
                        $express_types="ems";
                    }
                    if($express_type=="圆通"){
                        $express_types ="yuantong";
                    }
                    if($express_type=="申通"){
                        $express_types ="shentong";
                    }
                    if($express_type=="中通"){
                        $express_types ="zhongtong";
                    }
                    if($express_type=="韵达"){
                        $express_types ="yunda";
                    }

                    if(!empty($express_num)) {
                        $codes =$express_num;
                        //参数设置
                        $post_data = array();
                        $post_data["customer"] = '4C249BC13C74A7FE1ED2AAEACF722D34';
                        $key = 'rBJvVnui5301';
                        $post_data["param"] = '{"com":"'.$express_types.'","num":"' . $codes . '"}';
                        $url = 'http://poll.kuaidi100.com/poll/query.do';
                        $post_data["sign"] = md5($post_data["param"] . $key . $post_data["customer"]);
                        $post_data["sign"] = strtoupper($post_data["sign"]);
                        $o = "";
                        foreach ($post_data as $k => $v) {
                            $o .= "$k=" . urlencode($v) . "&";        //默认UTF-8编码格式
                        }
                        $post_data = substr($o, 0, -1);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                        $result = curl_exec($ch);
                        $data = str_replace("\"", '"', $result);
                        $data = json_decode($data,true);
                        session('by_order_id',null);
                    }
                }


            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * @return \think\response\View
     * 退款
     **************************************
     */
    public  function refund(Request $request){
        return view('refund');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 我的待支付订单点击支付返回数据
     **************************************
     */
    public function  read_order_to_pay(Request $request){
        if($request->isPost()){
            $data_id =$request->only(['id'])['id'];
            if(!empty($data_id)){
                $data = Db::name('order')->where('id',$data_id)->find();
                return ajax_success('成功返回',$data);
            }

        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 订单详情页面的付款按钮
     **************************************
     */
    public function order_to_pay_by_number(Request $request){
        if($request->isPost()){
            $order_numbers =$request->only(['id'])['id'];
            if(!empty($order_numbers)){
                $data = Db::name('order')->where('order_information_number',$order_numbers)->find();
                return ajax_success('成功返回数据',$data);
            }

        }
    }
}