<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/9
 * Time: 9:39
 */

namespace app\index\controller;
use think\Controller;

class GoodsList extends Controller{

    /**
     * 晒单
     * 陈绪
     */
    public function index(){
        return view("index");
    }


    public function particulars(){
        return view("particulars");
    }

}