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
    public function index(Request $request){
        if($request->isPost()){
            $category = db("goods_type")->where("status","<>","0")->select();
            return ajax_success("获取成功",$category);
        }
        if($request->isGet()){
            $category = db("goods_type")->where("status","<>","0")->where("pid","0")->select();
            $this->assign(["category"=>$category]);
        }
        return view('class_index');
    }


    /**
     * [商品分类显示]
     * 陈绪
     */
    public function show(Request $request){
        if ($request->isPost()){
            $goods_type = db("goods_type")->where("pid",$request->param("id"))->select();
            return ajax_success("获取成功",$goods_type);
        }
    }


}