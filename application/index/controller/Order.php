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
        $member_information =Db::name('user')->field('harvester,harvester_phone_num,city,address')->where('phone_num',$member['phone_num'])->find();
        $my_position =explode(",",$member_information['city']);
//        header("Content-Type:text/html; charset=utf-8");
//            $my_position = iconv("utf-8", "utf-8", $my_position[0]);
//        dump($my_position[0]);exit();
        $position = $my_position[0].$my_position[1].$my_position[2].$member_information['address'];
        if(!empty($my_position)){
            $this->assign('member_information',$member_information);
        }
        if(!empty($position)){
            $this->assign('position',$position);
        }
        $commodity_id =Session::get('goods_id');
        if(!empty($commodity_id)){
            $datas =Db::name('goods')->where('id',$commodity_id)->find();
//            $goods_name= $datas['goods_name'];
//            header("Content-Type:text/html; charset=utf-8");
//            $goods_name = iconv("utf-8", "utf-8", $goods_name);
            $goods_bottom_money=$datas['goods_bottom_money'];
            $goods_bottom_money =(string)$goods_bottom_money;
            $arr=explode(".",$goods_bottom_money);
            $express_fee =13.00;
            /*总费用*/
            $all_money = $goods_bottom_money + $express_fee;
            $data =[
                'commodity_id'=>$commodity_id,
                'goods_name'=>$datas['goods_name'],
                'goods_bottom_money'=>$goods_bottom_money,
                'goods_bottom_money_one'=>$arr[0],
                'goods_bottom_money_two'=>$arr[1],
                //运费
                'express_fee'=>$express_fee,
                //总计
                'all_money'=>$all_money

            ];
            $this->assign('data',$data);
        }
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
            $member_id =Db::name('user')->field('id')->where('phone_num',$member_data['phone_num'])->find();
            $commodity_id =Session::get('goods_id');
            $goods_data =Db::name('goods')->where('id',$commodity_id)->find();
            $create_time = time();
            if(!empty($data)){
               $datas =[
                   'goods_img'=>$goods_data['goods_show_images'],
                   'goods_name'=>$data['goods_name'],
                   'order_num'=>$data['order_num'],
                   'user_id'=>$member_id['id'],
                   'create_time'=>$create_time,
                   'pay_money'=>$data['all_pay'],
                   'status'=>1,
                   'goods_id'=>$commodity_id,
                   'send_money'=>$data['express_fee']
               ];
               $res =Db::name('order')->data($datas)->insert();
               if($res){
//                  Session::delete('goods_id');
                   $this->success('下单成功');
               }
               return ajax_success('获取成功',$datas);
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
                $express_num =Db::name('order')->field('express_num')->where('id',$order_id)->find();
                if(!empty($express_num)) {
                    $codes =$express_num['express_num'];
                    //参数设置
                    $post_data = array();
                    $post_data["customer"] = '4C249BC13C74A7FE1ED2AAEACF722D34';
                    $key = 'rBJvVnui5301';
                    $post_data["param"] = '{"com":"yuantong","num":"' . $codes . '"}';

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
    public  function refund(Request $request){
        return view('refund');
    }


}