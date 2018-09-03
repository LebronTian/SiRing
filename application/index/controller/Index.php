<?php

namespace app\index\controller;

use think\Controller;
class Index extends Controller
{
    /**
     * 首页
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */

    public function index()
    {
        $goods_type = db("goods_type")->where("pid","0")->select();
        $seckill = db("seckill")->select();
        $goods = db("goods")->where("goods_status",1)->select();
        return view("index",['goods_type'=>$goods_type,"seckill"=>$seckill,"goods"=>$goods]);
    }




}
