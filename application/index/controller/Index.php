<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Index extends Controller
{
    /**
     * 首页
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */

    public function index(Request $request)
    {
        if($request->isPost()) {
            $goods_type = db("goods_type")->where("pid", "0")->order("sort_number")->select();
            $seckill = db("seckill")->select();
            $goods = db("goods")->where("goods_status", 1)->select();
            return ajax_success("获取成功",array('goods_type'=>$goods_type,"seckill"=>$seckill,"goods"=>$goods));
        }else{
            $goods_type = db("goods_type")->where("pid", "0")->order("sort_number")->select();
            $seckill = db("seckill")->select();
            $goods = db("goods")->where("goods_status", 1)->select();
            return view("index",['goods_type'=>$goods_type,"seckill"=>$seckill,"goods"=>$goods]);

        }
    }

}
