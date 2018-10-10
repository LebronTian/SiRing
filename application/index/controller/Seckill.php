<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/15
 * Time: 18:43
 */

namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;

class Seckill extends Controller{


    /**
     * [秒杀列表]
     * 陈绪
     */
    public function index(Request $request){
        if ($request->isPost()){
            $type_id = $request->only(['type_id'])["type_id"];
            $goods = db("goods")->where("goods_type_id",$type_id)->select();
            Session("type_id",$type_id);
            return ajax_success("获取成功");
        }
    }



    public function show(Request $request){
        if($request->isPost()) {
            $type_id = Session::get("type_id");
            $goods = db("goods")->where("goods_type_id", $type_id)->select();
            $images = db("seckill")->where("type_id", $type_id)->select();
            $over_time = $images[0]["over_time"];
            $time = time();
            if (!empty($over_time)) {
                $over_time = $images[0]["over_time"];
                $str_time = $over_time;
                if ($over_time - $time <= 0) {
                    $over_time = date("Y-m-d H:i:s", strtotime("+3 day"));
                    $str_time = strtotime($over_time);
                }

            } else {
                $over_time = date("Y-m-d H:i:s", strtotime("+3 day"));
                $str_time = strtotime($over_time);
            }
            //获取time时间
            return ajax_success("获取成功", array("goods" => $goods, "images" => $images, "time" => $str_time));
        }
        return view("seckill_index");
    }


}