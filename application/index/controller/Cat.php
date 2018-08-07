<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/7
 * Time: 14:28
 */
namespace  app\index\controller;

use think\Controller;

class  Cat extends  Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 购物车首页
     **************************************
     */
    public  function  index(){
        return view('cat_index');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *订单详情
     **************************************
     */
    public function  detail(){
        return view('cat_detail');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 确认订单订单
     **************************************
     */
    public function order(){
        return view('cat_order');
    }


}