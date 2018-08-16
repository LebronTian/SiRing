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



        public function details(){
            return view('details');
        }
        public function myorder(){
            return view('myorder');
        }

}