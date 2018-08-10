<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/7
 * Time: 14:18
 */
namespace  app\index\controller;

use think\Controller;
use think\Request;

class  Classify extends  Controller{


    /**
     * [商品分类]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function  index(){
        $category = db("goods_type")->where("status","<>","0")->select();
        $goods = db("goods")->where("goods_status","<>","0")->select();
        return view('class_index',["category"=>$category,"goods"=>$goods]);
    }


    /**
     * [商品显示]
     * 陈绪
     */
    public function show(Request $request){
        if ($request->isPost()){
            $goods = db("goods")->where("goods_type_id",$request->param("goods_type_id"))->select();
            return ajax_success("获取成功",$goods);
        }

    }
}