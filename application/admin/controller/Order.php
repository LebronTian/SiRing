<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/11
 * Time: 11:42
 */
namespace  app\admin\controller;

use think\Controller;
use  think\Db;
use think\Request;

class  Order extends  Controller{

    /**
     **************李火生*******************
     * @return \think\response\View
     * 订单首页
     **************************************
     */
    public function index(){
        $data=Db::table("tb_order")
            ->field("tb_order.*,tb_user.user_name tname,tb_user.phone_num phone")
            ->join("tb_user","tb_order.user_id=tb_user.id",'left')
            ->paginate(10);
        $count =Db::name('order')->count();
        if(!empty($data)){
            $this->assign('count',$count);
            $this->assign('data',$data);
        }
        return view('order_index');
    }
    /**
     **************李火生*******************
     * @param Request $request
     * 模糊查询
     **************************************
     */
    public function search(Request $request){
        if($request->isPost()){
            $keywords =input('search_key');
            if(!empty($keywords)){
                $condition = " `goods_name` like '%{$keywords}%' or `id` like '%{$keywords}%' or`user_id` like '%{$keywords}%'";
                $res = Db::name("order")->where($condition)->select();
                return ajax_success('成功',$res);
            }
//            $timemin  =strtotime(input('datemin'));
//            $timemax  =strtotime(input('datemax'));
//            if(empty($timemin)||empty($timemax)){
//                $condition = " `goods_name` like '%{$keywords}%' or `id` like '%{$keywords}%' or`user_id` like '%{$keywords}%'";
//                $res = Db::name("order")->where($condition)->select();
//                dump($res);
//                return ajax_success('成功',$res);
//            }
//            if(!empty($timemin)&&!empty($timemax)){
//                if(empty($keywords)){
//                    $condition = "create_time>{$timemin} and create_time< {$timemax}";
//                    $res = Db::name("order")->where($condition)->select();
//                    return ajax_success('成功',$res);
//                }
//                if(!empty($keywords)){
//                    $condition = " `goods_name` like '%{$keywords}%' or `id` like '%{$keywords}%' or`user_id` like '%{$keywords}%'";
//                    $conditions = "create_time>{$timemin} and create_time< {$timemax}";
//                    $res = Db::name("order")->where($condition)->where($conditions)->select();
//                    return ajax_success('成功',$res);
//                }else{
//                    return ajax_error('失败');
//                }
//
//            }
        }
    }


    /**
     **************李火生*******************
     * 批量发货
     **************************************
     */
    public function batch_delivery(Request $request){
        if($request->isPost()){
            $id =$_POST['id'];
            if(is_array($id)){
                $where ='id in('.implode(',',$id).')';
            }else{
                $where ='id='.$id;
            }
            $list =  Db::name('order')->where($where)->update(['status'=>3]);
            if($list!==false)
            {
                $this->success('发货成功!');
            }else{
                $this->error('发货失败');
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * 代付款页面显示
     **************************************
     */
    public function pending_payment(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            if(!empty($order_id)){
                $res =Db::name('order')->where('id',$order_id)->find();
                if($res){
                    return ajax_success('成功',$res);
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 商家手动拒绝买家订单
     **************************************
     */
    public function refuse(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            if(!empty($order_id)){
                $res = Db::name('order')->where('id',$order_id)->update(['status'=>0]);
                if($res){
                    return ajax_success('已拒绝',$res);
                }
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * 添加快递单号并且把状态值改变，变为已发货
     **************************************
     */
    public function express_number(Request $request){
        if($request->isPost())
        {
            $order_id =$_POST['order_id'];
            $express_type=$_POST['express_type'];
            $express_num =$_POST['express_num'];
            $data =[
                'express_type'=>$express_type,
                'express_num'=>$express_num,
                'status'=>3
            ];
            if(!empty($express_num)){
              $res =  Db::name("order")->where('id',$order_id)->update($data);
              if($res){
                  $this->success('快递信息录入成功');
              }else{
                  $this->error('失败');
              }
            }
        }

    }

    /**
     **************李火生*******************
     * @param Request $request
     * 已发货查看的快递信息
     **************************************
     */
    public function order_deliver(Request $request){
        if($request->isPost()){
            $order_id =$_POST['order_id'];
            $deliver_res =Db::name('order')->field('express_type,express_num')->where('id',$order_id)->find();
          if($deliver_res){
              return ajax_success('成功',$deliver_res);
          }
        }
    }



    /**
     **************李火生*******************
     * @return \think\response\View
     * 晒单
     **************************************
     */
    public function sunburn(){

        return view('order_sunburn');
    }




}