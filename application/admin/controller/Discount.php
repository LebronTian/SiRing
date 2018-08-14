<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/13
 * Time: 14:46
 */

namespace app\admin\controller;
use think\Controller;

class Discount extends Controller{

    /**
     * 优惠券
     * 陈绪
     */
    public function index(){

        return view("discount_index");

    }

}