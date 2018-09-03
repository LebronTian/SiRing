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
        if(!empty($commodity_id)){
            $datas =Db::name('goods')->where('id',$commodity_id)->find();
            $goods_bottom_money=$datas['goods_bottom_money'];
            $express_fee =0.00;
            /*总费用*/
            $all_money = $goods_bottom_money + $express_fee;
            $data =[
                'commodity_id'=>$commodity_id,
                'goods_name'=>$datas['goods_name'],
                'goods_bottom_money'=>$goods_bottom_money,
                //运费
                'express_fee'=>$express_fee,
                //总计
                'all_money'=>$all_money
            ];
            $this->assign('data',$data);
        }
        //从购物车过来
        $shopping_id =Session::get('shopping');





        return view("index");
    }
    /**
     **************李火生*******************
     * @param Request $request
     * 提交订单
     **************************************
     */
    public function  bt_order(Request $request){
        if($request->isPost()){
            $data =$_POST;
            $member_data =session('member');
            $member =Db::name('user')->field('id,harvester,harvester_phone_num,city,address')->where('phone_num',$member_data['phone_num'])->find();
            if(empty($member['harvester']) ||empty($member['harvester_phone_num']) || empty($member['city']) ||empty($member['address'])){
                $this->error('请填写收货人信息');
            }
            if(!empty($member['city'])){
                $my_position =explode(",",$member['city']);
                $position = $my_position[0].$my_position[1].$my_position[2].$member['address'];
            }
            $commodity_id =Session::get('goods_id');
            $goods_data =Db::name('goods')->where('id',$commodity_id)->find();
            $create_time = time();
            if(!empty($data)){
               $datas =[
                   'goods_img'=>$goods_data['goods_show_images'],
                   'goods_name'=>$data['goods_name'],
                   'order_num'=>$data['order_num'],
                   'user_id'=>$member['id'],
                   'harvester'=>$member['harvester'],
                   'harvest_phone_num'=>$member['harvester_phone_num'],
                   'harvest_address'=>$position,
                   'create_time'=>$create_time,
                   'pay_money'=>$data['all_pay'],
                   'status'=>1,
                   'goods_id'=>$commodity_id,
                   'send_money'=>$data['express_fee'],
                   'order_information_number'=>$create_time.$member['id'],//时间戳+用户id构成订单号
               ];
               $res =Db::name('order')->insertGetId($datas);
               if($res){
                   Session::delete('goods_id');
                   session('order_id',$res);
                   return ajax_success('下单成功',$datas);
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
            $order_id = Session::get("order_id");
            if(!empty($order_id)){
                    $data=Db::table("tb_order")
                        ->field("tb_order.*,tb_goods.goods_bottom_money goods_bottom_money")
                        ->join("tb_goods","tb_order.goods_id=tb_goods.id and tb_order.id=$order_id",'left')
                        ->find();
                   $this->assign('data',$data);
//                   session('order_id',null);
                }
            return view('details');
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
            Session("order_id",$id);
            return ajax_success("获取成功");
        }
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 我的订单
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

    public function order_pay_test()
    {
        $aop = new AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = 'your app_id';
        $aop->rsaPrivateKey = '';
        $aop->alipayrsaPublicKey='请填写支付宝公钥，一行字符串';
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='GBK';
        $aop->format='json';
        $request = new AlipayTradeWapPayRequest ();
        $request->setBizContent("{" .
            "\"body\":\"对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。\"," .
            "\"subject\":\"大乐透\"," .
            "\"out_trade_no\":\"70501111111S001111119\"," .
            "\"timeout_express\":\"90m\"," .
            "\"time_expire\":\"2016-12-31 10:05\"," .
            "\"total_amount\":9.00," .
            "\"seller_id\":\"2088102147948060\"," .
            "\"auth_token\":\"appopenBb64d181d0146481ab6a762c00714cC27\"," .
            "\"goods_type\":\"0\"," .
            "\"passback_params\":\"merchantBizType%3d3C%26merchantBizNo%3d2016010101111\"," .
            "\"quit_url\":\"http://www.taobao.com/product/113714.html\"," .
            "\"product_code\":\"QUICK_WAP_WAY\"," .
            "\"promo_params\":\"{\\\"storeIdType\\\":\\\"1\\\"}\"," .
            "\"royalty_info\":{" .
            "\"royalty_type\":\"ROYALTY\"," .
            "        \"royalty_detail_infos\":[{" .
            "          \"serial_no\":1," .
            "\"trans_in_type\":\"userId\"," .
            "\"batch_no\":\"123\"," .
            "\"out_relation_id\":\"20131124001\"," .
            "\"trans_out_type\":\"userId\"," .
            "\"trans_out\":\"2088101126765726\"," .
            "\"trans_in\":\"2088101126708402\"," .
            "\"amount\":0.1," .
            "\"desc\":\"分账测试1\"," .
            "\"amount_percentage\":\"100\"" .
            "          }]" .
            "    }," .
            "\"extend_params\":{" .
            "\"sys_service_provider_id\":\"2088511833207846\"," .
            "\"hb_fq_num\":\"3\"," .
            "\"hb_fq_seller_percent\":\"100\"," .
            "\"industry_reflux_info\":\"{\\\\\\\"scene_code\\\\\\\":\\\\\\\"metro_tradeorder\\\\\\\",\\\\\\\"channel\\\\\\\":\\\\\\\"xxxx\\\\\\\",\\\\\\\"scene_data\\\\\\\":{\\\\\\\"asset_name\\\\\\\":\\\\\\\"ALIPAY\\\\\\\"}}\"," .
            "\"card_type\":\"S0JP0000\"" .
            "    }," .
            "\"sub_merchant\":{" .
            "\"merchant_id\":\"19023454\"," .
            "\"merchant_type\":\"alipay: 支付宝分配的间连商户编号, merchant: 商户端的间连商户编号\"" .
            "    }," .
            "\"enable_pay_channels\":\"pcredit,moneyFund,debitCardExpress\"," .
            "\"disable_pay_channels\":\"pcredit,moneyFund,debitCardExpress\"," .
            "\"store_id\":\"NJ_001\"," .
            "\"settle_info\":{" .
            "        \"settle_detail_infos\":[{" .
            "          \"trans_in_type\":\"cardSerialNo\"," .
            "\"trans_in\":\"A0001\"," .
            "\"summary_dimension\":\"A0001\"," .
            "\"amount\":0.1" .
            "          }]" .
            "    }," .
            "\"invoice_info\":{" .
            "\"key_info\":{" .
            "\"is_support_invoice\":true," .
            "\"invoice_merchant_name\":\"ABC|003\"," .
            "\"tax_num\":\"1464888883494\"" .
            "      }," .
            "\"details\":\"[{\\\"code\\\":\\\"100294400\\\",\\\"name\\\":\\\"服饰\\\",\\\"num\\\":\\\"2\\\",\\\"sumPrice\\\":\\\"200.00\\\",\\\"taxRate\\\":\\\"6%\\\"}]\"" .
            "    }," .
            "\"specified_channel\":\"pcredit\"," .
            "\"business_params\":\"{\\\"data\\\":\\\"123\\\"}\"," .
            "\"ext_user_info\":{" .
            "\"name\":\"李明\"," .
            "\"mobile\":\"16587658765\"," .
            "\"cert_type\":\"IDENTITY_CARD\"," .
            "\"cert_no\":\"362334768769238881\"," .
            "\"min_age\":\"18\"," .
            "\"fix_buyer\":\"F\"," .
            "\"need_check_info\":\"F\"" .
            "    }" .
            "  }");
        $result = $aop->pageExecute ( $request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }


}