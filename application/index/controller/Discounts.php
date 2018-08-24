<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/24
 * Time: 11:20
 */

namespace app\index\controller;
use think\Controller;

class Discounts extends Controller{

    /**
     * 优惠券
     * 陈绪
     */
    public function index(){
        return view("discounts_index");
    }



    /**
     * 我的优惠券
     * 陈绪
     */
    public function discounts_my(){
        return view("discounts_my");
    }

}