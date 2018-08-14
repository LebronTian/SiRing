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
        $this->assign('data',$data);
        return view('order_index');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 获取订单信息
     **************************************
     */
    public function order_info(Request $request){
        if($request->isPost()){
            $data =Db::name('order')->order('create_time',"desc")->select();
//            return ajax_success('获取成功',$data);
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