<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/7
 * Time: 14:18
 */
namespace  app\index\controller;

use think\Controller;

class  Classify extends  Controller{


    /**
     * [商品分类]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function  index(){
        $category = db("goods_type")->where("status","<>","0")->select();
        return view('class_index',["category"=>$category]);
    }
}