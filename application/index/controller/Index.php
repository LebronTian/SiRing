<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Session;

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
            $images = db("advertising")->select();
            $recommend = db("recommend")->where("status","<>","0")->select();
            $time = time();
            foreach ($seckill as $value){
                $str_time = $value["over_time"];
                if (!empty($value["over_time"])) {
                    if ($value["over_time"] - $time <= 0) {
                        $value["over_time"] = date("Y-m-d H:i:s", strtotime("+3 day"));
                        $str_time = strtotime($value["over_time"]);
                    }
                }
            }

            if($goods){
                return ajax_success("获取成功",array("recommend"=>$recommend,'images'=>$images,'goods_type'=>$goods_type,"seckill"=>$seckill,"goods"=>$goods,"time"=>$str_time));
            }else{
                return ajax_error("获取失败");
            }
        }
        if($request->isGet()){
            $goods_type = db("goods_type")->where("pid", "0")->where("status","<>",0)->order("sort_number")->limit(7)->select();
            $seckill = db("seckill")->select();
            $goods = db("goods")->where("goods_status", 1)->select();
            $recommend = db("recommend")->where("status","<>","0")->select();
            $time = time();
            foreach ($seckill as $value){
                $str_time = $value["over_time"];
                if (!empty($value["over_time"])) {
                    if ($value["over_time"] - $time <= 0) {
                        $value["over_time"] = date("Y-m-d H:i:s", strtotime("+3 day"));
                        $str_time = strtotime($value["over_time"]);
                    }
                }
            }
            $this->assign(['goods_type'=>$goods_type,"seckill"=>$seckill,"goods"=>$goods,"recommend"=>$recommend]);
        }
        return view("index");
    }

}
