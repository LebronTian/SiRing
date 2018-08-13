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


class Order extends Controller{

    /**
     * [订单]
     * 陈绪
     */
    public function index(){
        return view("index");
    }

    /**
     **************李火生*******************
     * 购买商品时候需要绑定的用户id
     **************************************
     */
    public function  common_id(){
        $data =$_SESSION['member'];
        dump($data);exit();
        if($data){
            $member_id =Db::name('user')->field('id')->where('phone_num',$data['phone_num'])->find();
            return ajax_success('成功',$member_id);
        }
        if(!isset($data)){
            $this->redirect('index/Login/login');
        }
    }


}