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
        $data =Db::name('order')->order('create_time',"desc")->select();
//        $member_id =Db::name('order')->field('user_id')->order('create_time',"desc")->select();
//        foreach ($member_id as $key =>  $val){
//            $member_data = Db::name('user')->field('user_name,phone_num')->where('id',$val['user_id'])->find();
//        }

        if(!empty($data)){
            $this->assign('data',$data);
//            $this->assign('member_data',$member_data[]);
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
            $timemin  =strtotime(input('datemin'));
            $timemax  =strtotime(input('datemax'));
            if(empty($timemin)||empty($timemax)){
                $condition = " `goods_name` like '%{$keywords}%' or `id` like '%{$keywords}%' or`user_id` like '%{$keywords}%'";
                $res = Db::name("order")->where($condition)->select();
                return ajax_success('成功',$res);
            }
            if(!empty($timemin)&&!empty($timemax)){
                if(empty($keywords)){
                    $condition = "create_time>{$timemin} and create_time< {$timemax}";
                    $res = Db::name("order")->where($condition)->select();
                    return ajax_success('成功',$res);
                }
                if(!empty($keywords)){
                    $condition = " `goods_name` like '%{$keywords}%' or `id` like '%{$keywords}%' or`user_id` like '%{$keywords}%'";
                    $conditions = "create_time>{$timemin} and create_time< {$timemax}";
                    $res = Db::name("order")->where($condition)->where($conditions)->select();
                    return ajax_success('成功',$res);
                }else{
                    return ajax_error('失败');
                }

            }
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
                $this->success('更新成功!');
            }else{
                $this->error('更新失败');
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
     * @return \think\response\View
     * 晒单
     **************************************
     */
    public function sunburn(){

        return view('order_sunburn');
    }




}