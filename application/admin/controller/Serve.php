<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/17
 * Time: 17:15
 */

namespace app\admin\controller;
use think\Controller;

class Serve extends Controller{


    /**
     * 售后维修处理
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){
        return view("serve_index");
    }


    /**
     * 售后原因处理
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function listing(){
        return view("serve_listing");
    }

}