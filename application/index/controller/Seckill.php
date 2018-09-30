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
            $type_id = $request->only(['type_id'])["type_id"];
            return ajax_success("获取成功");
        }
        return view("seckill_index");


    }


}