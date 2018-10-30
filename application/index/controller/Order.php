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
                        return ajax_success('下单成功', $datas['order_information_number']);
                    }
                }
            }
            //从购物车过来的
//            $shopping_id = Session::get('shopping');
//            if (!empty($shopping_id)) {
//                //TODO:
////                Session::delete('goods_id');
//                $shopping = Db::name('shopping_shop')->where('id', $shopping_id['id'])->find();
//                $shop_id = explode(',', $shopping['shopping_id']);
//                if (is_array($shop_id)) {
//                    $where = 'id in(' . implode(',', $shop_id) . ')';
//                } else {
//                    $where = 'id=' . $shop_id;
//                }
//                $list = Db::name('shopping')->where($where)->select();
//                $create_time = time();
//                foreach ($list as $k => $v) {
//                    if (!empty($data)) {
//                        $datas = [
//                            'goods_img' => $v['goods_images'],
//                            'goods_name' => $data['goods_name'][$k],
//                            'order_num' => $data['order_num'][$k],
//                            'user_id' => $member['id'],
//                            'harvester' => $member['harvester'],
//                            'harvest_phone_num' => $member['harvester_phone_num'],
//                            'harvest_address' => $position,
//                            'create_time' => $create_time,
//                            'pay_money' => $data['all_pay'],
//                            'status' => 1,
//                            'goods_id' => $v['goods_id'],
//                            'send_money' => $data['express_fee'],
//                            'order_information_number' => $create_time . $member['id'],//时间戳+用户id构成订单号
//                            'shopping_shop_id' => $v['id']
//                        ];
//                        $res =Db::name('order')->insertGetId($datas);
////                        session('order_id', $res);
//                        /*下单成功对购物车里面对应的商品进行删除*/
//
//                    }
//
//                }
//                if ($res) {
//                    Session::delete('shopping');
//                    Db::name('shopping')->where($where)->delete();
//                    Db::name('shopping_shop')->where('id',$shopping_id['id'])->delete();
//                    return ajax_success('下单成功', $datas);
//                }
//
//
//            }
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
//            $order_num ='1540519884103';
            $product_code ="QUICK_MSECURITY_PAY";
            $out_trade_no="ZQLM3O56MJD4SK3";
            $time =date('Y-m-d H:i:s');
            if(!empty( $order_num)){
                $data = Db::name('order')->where('order_information_number',$order_num)->select();
               if(!empty($data)){
                   foreach ($data as $k=>$v){
                       $goods_name = $v['goods_name'];
                       $order_num = $v['order_information_number'];
                       $goods_pay_money =$v['pay_money'];
                       $subject =$v['order_num'];
//                       $curl_url ='goods_name='.$goods_name.'&'.'order_num='.$order_num.'&'."goods_pay_money=".$goods_pay_money;
//                       $app_id ="app_id=2016112603335050"."&biz_content={'timeout_express':'30m','seller_id':"."'".$order_num."'".",'product_code':"."'".$product_code."'".",'total_amount':"."'".$goods_pay_money."'".",'subject':"."'".$subject."'".",'body':"."'".$goods_name."'".",'out_trade_no':"."'".$out_trade_no."'"."}&charset=utf-8&method=alipay.trade.app.pay&sign_type=RSA2&timestamp=".$time."&version=1.0";
                       $app_id='{"body":"我是测试数据","pay_test":"1","out_trade_no":"IQJZSRC1YMQB5HU","timeout_express":"30m","product_code":"QUICK_MSECURITY_PAY","total_amount":"0.01","subject":"1"}';
                       $app_ids =urlencode($app_id);

//                       $con ='&charset=utf-8&format=json&method=alipay.trade.app.pay&notify_url=http://domain.merchant.com/payment_notify&sign_type=RSA2&timestamp='.$time.'&version=1.0&sign=MIIEowIBAAKCAQEAz+SfWrndsOSD3AY3v5YtA9n+BoBcckMYfjgpIrT5Bu2YF2GR5oFCBJASSQeRRyDHPWL3i91lbyZeiBsE2l+rJcMTP+EfH6MpxMerwqfvOPw4p4OHHAnbI52xjdNZStBdIT7oEwEUsghuejCpWelL/b3CPFpW/1OpEVRnssw9gc0f1mius2eOXZ0+5JaJRZ/zJWxgyMHctF6NXcSG2oVOl0WyiNK/F4CuqdIcq1y8ZDiVvmRbyfzcEmbgob7MpwVFWw1Fge3z4fSnG7bicOJSXkPbWNhZmGe/yXCEXbA/8Kldp/nMkwnMGJ5A/3yFZTEUnmY60qnXA5T3R1KOnpXklwIDAQABAoIBAQCDq2VSbQ4AD3uES1vbuB3ipprBO2NR6zUEHEXReZWP0cPWazGhMJTDlww9vNFCn3wRYTEwIJUyBLcytQop1RXs4NS8TLUNsKWvwFcE/qABE54+WoukMonc0O+3x/hx7e5ONC2Ae9rDt5thQJjCHYTHvPvchcs8A5y9IRxcngcGweL6m6KUd4yT4yr5pPCXM8Q4B5cG/BM+MtLeqPJ1S7zheMKt4pN52M9pU9+n1V3nx1FgViv7ycOh8E+9L33S/Ri9HuLyIeV9zZ44g53ociUlSoQBnUIiDHWriHROWP0yxPdp0Et4oUPFcsDR1FVa8rFSmhZRauA6M7Um8SRXKVtBAoGBAPrhPqij8HfnOCJAGMcbwJnpQGZAypYBawEOSib3uIKyqEQmDlvzTjJgR2YbFUfGvgeAn0mX/Q4B/Vgffb1dqCJbU4McSE3GHJHCdBO6UqvUD4B8Qy6aJJomPGwgZAi+DAk9PtNDo2tC6DTZbd5UJMTqdpMq0776pjR6E3+7F2wZAoGBANQiyZTfw9qqf9xyQ4YKwu6v0165e+mnlycOTRkrBSESJUNSCH4aYHZnE4B9J1MU5fxxrZuk5qt6iu0N5AkUQY6xuLkKdjX8WJbHWgHHjvMxXsEqx1LQlQ2PSCCvF5jxB0xhzTjBa3uCzfabs3o+6MKh1QF1DuYMBE1B/rku8uwvAoGAdnuAIxbhj08EpLBOw2Ho8QdGocQBqRxcU7BS9tpRKnCDpUOvzl820/XCYodx4mcLAfINyCzelwn7gu3EbXVY3XjyFN57izd/8Jq8RUDeoEXTWGPXOqATnzVlnc8iTzqp5oclL5MnD5YWojb5e2GTx+fPPiuguvYXHnt00AMkyakCgYAkFOKqksDSUYu76Cd6BhyP0pImG3BrFplMCE+uxzVxIY/6+ln9cOkVWoTjpuXoaLaRkJhRz+N4KTi2B1XRAYQBDFN6DcB7gDdlNfUmNlYnIS+XtXn/qQChNMy02nMuDVkLcdshGyz37hCwMF1/nnGioToErG9jS4nzxhTYVJb2+wKBgGGU5XtXTZAeBtueAgwwPkdOe1pXHXjkeytG2cGeNJrBkMmj7B7eNt+3EkHw4yPgvj/e4OYNm4ojRH05FefZmb6dtLDUH0p1k9LeqEbGGbHn7cl7jDjTqaRznlODyaT3pJlRldZIaJ95VEwZtpMQCnItUAu5yGH3Vrgo2Y8eNpAn';
                       $con ='&charset=utf-8&format=json&method=alipay.trade.app.pay&notify_url=http://domain.merchant.com/payment_notify&sign_type=RSA2&timestamp='.$time.'&version=1.0&sign=4dA15K7C5y20LMfGoAwVJrfK4nyiKhn4UKJCewkW5WoeYETUZGC%2FYkZX6IY9syAbe0opzJ9XPwuxsmJ2LgkkLrDJmfMXBEaZOw3fS0a6KIAPdo%2FtqOFzlAUSiGP79yyorSDzXVSNk7gZPhuYqW2ym1wAWiuAz7WwDBg%2FS1Ns2brrfpen%2BXncPabc5CdmwYhi9pKE%2BwxWeZv825f5EAV%2FAic%2FcPEyBwic6BVjYt3gGff1TKdzMysxsZn3ElLpcO3hs%2FPOopwcBb85JsVKf9v9hV9y%2FY6CHlfdKeXAVDXra0eHm9LpQZwGBSV%2BabTCZZRXqRxsdR1Mp%2BTlgFzap49XqA%3D%3D';
//                         $app_id ="{'timeout_express':'30m','seller_id':"."'".$order_num."'".",'product_code':"."'".$product_code."'".",'total_amount':"."'".$goods_pay_money."'".",'subject':"."'".$subject."'".",'body':"."'".$goods_name."'".",'out_trade_no':"."'".$out_trade_no."'"."}&charset=utf-8&method=alipay.trade.app.pay&sign_type=RSA2&timestamp=".$time."&version=1.0&sign=MIIEowIBAAKCAQEAz+SfWrndsOSD3AY3v5YtA9n+BoBcckMYfjgpIrT5Bu2YF2GR5oFCBJASSQeRRyDHPWL3i91lbyZeiBsE2l+rJcMTP+EfH6MpxMerwqfvOPw4p4OHHAnbI52xjdNZStBdIT7oEwEUsghuejCpWelL/b3CPFpW/1OpEVRnssw9gc0f1mius2eOXZ0+5JaJRZ/zJWxgyMHctF6NXcSG2oVOl0WyiNK/F4CuqdIcq1y8ZDiVvmRbyfzcEmbgob7MpwVFWw1Fge3z4fSnG7bicOJSXkPbWNhZmGe/yXCEXbA/8Kldp/nMkwnMGJ5A/3yFZTEUnmY60qnXA5T3R1KOnpXklwIDAQABAoIBAQCDq2VSbQ4AD3uES1vbuB3ipprBO2NR6zUEHEXReZWP0cPWazGhMJTDlww9vNFCn3wRYTEwIJUyBLcytQop1RXs4NS8TLUNsKWvwFcE/qABE54+WoukMonc0O+3x/hx7e5ONC2Ae9rDt5thQJjCHYTHvPvchcs8A5y9IRxcngcGweL6m6KUd4yT4yr5pPCXM8Q4B5cG/BM+MtLeqPJ1S7zheMKt4pN52M9pU9+n1V3nx1FgViv7ycOh8E+9L33S/Ri9HuLyIeV9zZ44g53ociUlSoQBnUIiDHWriHROWP0yxPdp0Et4oUPFcsDR1FVa8rFSmhZRauA6M7Um8SRXKVtBAoGBAPrhPqij8HfnOCJAGMcbwJnpQGZAypYBawEOSib3uIKyqEQmDlvzTjJgR2YbFUfGvgeAn0mX/Q4B/Vgffb1dqCJbU4McSE3GHJHCdBO6UqvUD4B8Qy6aJJomPGwgZAi+DAk9PtNDo2tC6DTZbd5UJMTqdpMq0776pjR6E3+7F2wZAoGBANQiyZTfw9qqf9xyQ4YKwu6v0165e+mnlycOTRkrBSESJUNSCH4aYHZnE4B9J1MU5fxxrZuk5qt6iu0N5AkUQY6xuLkKdjX8WJbHWgHHjvMxXsEqx1LQlQ2PSCCvF5jxB0xhzTjBa3uCzfabs3o+6MKh1QF1DuYMBE1B/rku8uwvAoGAdnuAIxbhj08EpLBOw2Ho8QdGocQBqRxcU7BS9tpRKnCDpUOvzl820/XCYodx4mcLAfINyCzelwn7gu3EbXVY3XjyFN57izd/8Jq8RUDeoEXTWGPXOqATnzVlnc8iTzqp5oclL5MnD5YWojb5e2GTx+fPPiuguvYXHnt00AMkyakCgYAkFOKqksDSUYu76Cd6BhyP0pImG3BrFplMCE+uxzVxIY/6+ln9cOkVWoTjpuXoaLaRkJhRz+N4KTi2B1XRAYQBDFN6DcB7gDdlNfUmNlYnIS+XtXn/qQChNMy02nMuDVkLcdshGyz37hCwMF1/nnGioToErG9jS4nzxhTYVJb2+wKBgGGU5XtXTZAeBtueAgwwPkdOe1pXHXjkeytG2cGeNJrBkMmj7B7eNt+3EkHw4yPgvj/e4OYNm4ojRH05FefZmb6dtLDUH0p1k9LeqEbGGbHn7cl7jDjTqaRznlODyaT3pJlRldZIaJ95VEwZtpMQCnItUAu5yGH3Vrgo2Y8eNpAn";
//                       $app ="app_id=2016112603335050"."&biz_content=".$app_ids ;
                       $app ="alipay_sdk=alipay-sdk-java-dynamicVersionNo&app_id=2018031402370789"."&biz_content=".$app_ids.$con ;

                       $deco ='%7B%22body%22%3A%22pay_test%22%2C%22out_trade_no%22%3A%22201810291619487174012%22%2C%22passback_params%22%3A%22callback+params%22%2C%22product_code%22%3A%22QUICK_MSECURITY_PAY%22%2C%22subject%22%3A%22%E4%BA%AE%E6%9E%9C%E5%9B%BD%E9%99%85%22%2C%22timeout_express%22%3A%2230m%22%2C%22total_amount%22%3A%22430.00%22%7D&charset=UTF-8&format=json&method=alipay.trade.app.pay&notify_url=https%3A%2F%2Flggj.lgz666.com%2Flggj%2Fapi%2Falipay%2Fnotify_url';
                       $data_name =urldecode($deco);



                       $body_content = '{"body":"pay_test","out_trade_no":"201810291619487174012","passback params":"callback_params","product_code":"QUICK_MSECURITY_PAY","subject":"亮锅","timeout_express":"30m","total_amount":"0.01"}';
                       $body_contents =urlencode($body_content);
                       $body_url='&charset=UTF-8&format=json&method=alipay.trade.app.pay&notify_url=';
                       $body_ec_url="https://lggj.lgz666.com/lggj/api/alipay/notify_url";
                       $body_encode_url =urlencode($body_ec_url);
                       $cont ='&sign=4dA15K7C5y20LMfGoAwVJrfK4nyiKhn4UKJCewkW5WoeYETUZGC%2FYkZX6IY9syAbe0opzJ9XPwuxsmJ2LgkkLrDJmfMXBEaZOw3fS0a6KIAPdo%2FtqOFzlAUSiGP79yyorSDzXVSNk7gZPhuYqW2ym1wAWiuAz7WwDBg%2FS1Ns2brrfpen%2BXncPabc5CdmwYhi9pKE%2BwxWeZv825f5EAV%2FAic%2FcPEyBwic6BVjYt3gGff1TKdzMysxsZn3ElLpcO3hs%2FPOopwcBb85JsVKf9v9hV9y%2FY6CHlfdKeXAVDXra0eHm9LpQZwGBSV%2BabTCZZRXqRxsdR1Mp%2BTlgFzap49XqA%3D%3D&sign_type=RSA2&timestamp='.$time.'&version=1.0';
                        $body_all ="alipay_sdk=alipay-sdk-java-dynamicVersionNo&app_id=2018031402370789"."&biz_content=".$body_contents.$body_url.$body_encode_url.$cont;
                       return ajax_success('数据成功返回',$body_all);


                   }
               }else{
                   return ajax_error('数据返回不成功',['status'=>0]);
               }
            }else{
                return ajax_error('失败',['status=>0']);
            }
        }

    }








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
                            'pay_money' => $data['all_pay'],
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
                   return ajax_error('待支付数据返回为空',['status'=>0]);
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
                    return ajax_error('待发IOS接口数据返回为空',['status'=>0]);
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
                        return ajax_error('待收货IOS接口数据返回为空',['status'=>0]);
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
                  return ajax_error('iso与待评价的接口数据返回失败',['status'=>0]);
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