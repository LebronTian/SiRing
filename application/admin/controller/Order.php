<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/11
 * Time: 11:42
 */
namespace  app\admin\controller;

use think\Controller;

class  Order extends  Controller{

    /**
     **************李火生*******************
     * @return \think\response\View
     * 订单首页
     **************************************
     */
    public function index(){

        return view('order_index');
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