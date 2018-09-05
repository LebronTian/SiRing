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
        if(!empty($commodity_id)){
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
                //运费
                'express_fee'=>$express_fee,
                //总计
                'all_money'=>$all_money
            ];
            $this->assign('data',$data);
        };
        //从购物车过来
        $shopping_id =Session::get('shopping');
        if(!empty($shopping_id)){
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
                        Session::delete('goods_id');
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
                Session::delete('goods_id');
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
                        session('order_id', $res);
                        /*下单成功对购物车里面对应的商品进行删除*/
                        dump($res);exit();

                    }
                }
                if ($res) {
                    Session::delete('shopping');
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
            $order_from_shop_id = Session::get("order_id");
            if(!empty($order_from_shop_id)){
                /*先通过查找订单编号*/
                $order_id =$order_from_shop_id;
                if(!empty($order_id)){
                    /*先清除之前的*/
                    $data=Db::table("tb_order")
                        ->field("tb_order.*,tb_goods.goods_bottom_money goods_bottom_money")
                        ->join("tb_goods","tb_order.goods_id=tb_goods.id",'left')
                        ->where('tb_order.id',$order_id)
                        ->find();
                    session('order_id',null);
                    $this->assign('data',$data);
                }

            }
            /*判断来自于我的订单列表点击订单详情*/
            $order_from_myorder_bt =Session::get('order_id_from_myorder');
            if(!empty($order_from_myorder_bt)){
                    $data=Db::table("tb_order")
                        ->field("tb_order.*,tb_goods.goods_bottom_money goods_bottom_money")
                        ->join("tb_goods","tb_order.goods_id=tb_goods.id",'left')
                        ->where('tb_order.id',$order_from_myorder_bt)
                        ->find();
                    $this->assign('data',$data);
                    session('order_id_from_myorder',null);
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




}