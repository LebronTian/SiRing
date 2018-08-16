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

class Seckill extends Controller{


    /**
     * [秒杀列表]
     * 陈绪
     */
    public function index(Request $request){
        if ($request->isPost()){
            $seckill = db("seckill")->select();
            $goods = db("goods")->select();
            foreach ($seckill as $key=>$value){
                foreach ($goods as $val){
                    if($value['goods_id'] == $val['id']){
                        $seckill[$key]['goods_name'] = $val['goods_name'];
                        $seckill[$key]['goods_show_images'] = $val['goods_show_images'];
                    }
                }
            }
            return ajax_success("获取成功",$seckill);
        }
        return view("seckill_index");


    }


}