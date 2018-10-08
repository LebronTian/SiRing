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
            $over_time = Session::get("over_time");
            $time = time();
            if (!empty($over_time)) {
                $over_time = Session::get("over_time");
                $str_time = $over_time;
                if ($over_time - $time <= 0) {
                    $over_time = date("Y-m-d H:i:s", strtotime("+3 day"));
                    $str_time = strtotime($over_time);
                    Session("over_time", $str_time);
                }

            } else {
                $over_time = date("Y-m-d H:i:s", strtotime("+3 day"));
                $str_time = strtotime($over_time);
                Session("over_time", $str_time);
            }
            if($goods){
                return ajax_success("获取成功",array('images'=>$images,'goods_type'=>$goods_type,"seckill"=>$seckill,"goods"=>$goods,"time"=>$str_time));
            }else{
                return ajax_error("获取失败");
            }
        }
        if($request->isGet()){
            $goods_type = db("goods_type")->where("pid", "0")->order("sort_number")->select();
            $seckill = db("seckill")->select();
            $goods = db("goods")->where("goods_status", 1)->select();
            $this->assign(['goods_type'=>$goods_type,"seckill"=>$seckill,"goods"=>$goods]);
        }
        return view("index");
    }

}
