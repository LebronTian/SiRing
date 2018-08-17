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


class Order extends Controller{

    /**
     **************李火生*******************
     * @return \think\response\View
     * 订单
     **************************************
     */
    public function index(){
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
                  Session::delete('goods_id');
                   $this->success('下单成功');
               }
               return ajax_success('获取成功',$data);
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
            $data =Db::name('order')->select();
            if(!empty($data)){
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
        public function myorder(){
            $data =Db::name('order')->order('create_time','desc')->select();
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
            $data =Db::name('order')->where('status',1)->order('create_time','desc')->select();
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
            $data =Db::name('order')->where('status',2)->order('create_time','desc')->select();
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
            $data =Db::name('order')->where('status',3)->whereOr('status',4)->order('create_time','desc')->select();
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
            $data =Db::name('order')->where('status',5)->whereOr('status',6)->order('create_time','desc')->select();
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
                    $res =Db::name('order')->where('id',$order_id)->update(['status',11]);
                    if($res){
                        return ajax_success('订单取消成功',$res);
                    }else{
                        return ajax_error('订单取消失败');
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
        public function confirm_collect_goods(Request $request){
            if ($request->isPost()){
                $order_id =$_POST['order_id'];
                if(!empty($order_id)){
                    $res =Db::name('order')->where('id',$order_id)->update(['status',5]);
                    if($res){
                        return ajax_success('确认收货成功',$res);
                    }else{
                        return ajax_error('确认收货失败');
                    }
                }
            }
        }




}