<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/7
 * Time: 14:28
 */
namespace  app\index\controller;

use think\Controller;
use think\Request;

class  Cat extends  Controller{

    /**
     * [商品显示]
     * 陈绪
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public  function  index(Request $request){

        return view('cat_index');
    }

    /**
     * [商品详情]
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function  detail(Request $request){
        if ($request->isPost()){
            $id = $request->only(['id']);
            halt($id);
            $goods = db("goods")->where("goods_status","<>","0")->where("id",$request->param('id'))->select();
            $goods_images = db("goods_images")->select();
            return ajax_success("获取成功",array("goods"=>$goods,"goods_images"=>$goods_images));
        }

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