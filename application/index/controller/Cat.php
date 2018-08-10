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
use think\Session;

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
     * [商品id]
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function ajax_id(Request $request){
        if ($request->isPost()){
            $id = $request->only(['id'])['id'];
            Session('id',$id);
            return ajax_success("获取成功");
        }
    }


    /**
     *[商品详情]
     * 陈绪
     */
    public function detail(Request $request)
    {
        if ($request->isPost()) {
            $id = Session::get("id");
            $goods = db("goods")->where("goods_status", "<>", "0")->where("id", $id)->select();
            $goods_images = db("goods_images")->select();
            foreach ($goods as $key => $value) {
                foreach ($goods_images as $val) {
                    if ($value['id'] == $val['goods_id']) {
                        $goods[$key]["goods_images"][] = $val["goods_images"];
                    }
                }
            }
            return ajax_success("获取成功", $goods);
        }
        return view("cat_detail");
    }



}