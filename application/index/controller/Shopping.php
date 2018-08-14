<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/10
 * Time: 15:43
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;

class Shopping extends Controller{

    /**
     * 购物车
     * 陈绪
     */
    public function index(){
        return view("index");
    }


    /**
     *获取商品id
     * 陈绪
     */
    public function ajax_id(Request $request){
        if ($request->isPost()){
            $data = Session::get("member");
            if(empty($data)){
                $this->error("还没有登录",url("index/Login/login"));
            }else{
                $user_id = db("user")->where("phone_num",$data['phone_num'])->field("id")->find();
                session("user_id",$user_id);
                $goods_id = $request->only(['id'])['id'];
                session("goods_id",$goods_id);
                return ajax_success("获取成功");
            }
        }

    }


}