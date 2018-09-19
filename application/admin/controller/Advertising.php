<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/19
 * Time: 14:03
 */
namespace app\admin\controller;
use think\Controller;

class Advertising extends Controller{


    /**
     * 广告管理列表
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){
        return view("advertising_index");
    }



    /**
     * 广告图片
     * 陈绪
     */
    public function add(){

        return view("advertising_add");

    }


    /**
     * 广告图片添加入库
     * 陈绪
     */
    public function save(){

    }

}