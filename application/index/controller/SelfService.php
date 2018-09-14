<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/11
 * Time: 17:05
 */
namespace  app\index\controller;
use think\Controller;
use think\Session;

class  SelfService extends  Controller{

    /**
     * 售后服务列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){
        return view('index');
    }



    /**
     * 售后维修
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function repair(){
        $data = Session::get("member");
        $user_id =db("user")->field('id')->where('phone_num',$data['phone_num'])->find();
        $order = db("order")->where("status",">=",5)->where("status","<=",7)->whereOr("status",10)->select();
        $serve = [];
        foreach ($order as $key=>$value){
            if($user_id["id"] == $value["user_id"]) {
                $goods = db("goods")->where("id", $value["goods_id"])->field("goods_show_images")->find();
                $serve[$key]["images"] = $goods["goods_show_images"];
                $serve[$key]["goods_name"] = $value["goods_name"];
                $serve[$key]["user_id"] = $value["user_id"];
                $serve[$key]["status"] = $value["status"];
                $serve[$key]["order_money"] = $value["pay_money"];
                $serve[$key]["order_num"] = $value["order_num"];
                $serve[$key]["create_time"] = $value["create_time"];
            }
        }
        return view('repair',["serve"=>$serve]);
    }



    /**
     * 问题描述
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function repair_desc(){
        return view('repair_desc');
    }


    /**
     * 提交成功
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
     public function successful_sub(){
        return view('successful_sub');
    }


    /**
     * 提交成功
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function processing(){
        return view('processing');
    }


    /**
     * 评价
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function evaluation(){
        return view('evaluation');
    }



    /**
     * 服务单详情
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function detail_info(){
        return view('detail_info');
    }
}