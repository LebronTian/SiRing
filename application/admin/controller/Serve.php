<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/17
 * Time: 17:15
 */

namespace app\admin\controller;
use think\Controller;
use think\Request;

class Serve extends Controller{


    /**
     * 售后维修处理
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(Request $request){
        if($request->isPost()) {
            $serve = db("serve")->select();
            return ajax_success("获取成功",$serve);
        }
        return view("serve_index");

    }



    /**
     * 售后状态修改
     * 陈绪
     */
    public function status(Request $request){
        if($request->only(['status'])['status'] == ""){
            return 0;
        }else {
            $status['reply'] = $request->only(['reply'])["reply"];
            $status["status"] = $request->only(["status"])["status"];
            $id = $request->only(['id'])['id'];
            $status["dispose_time"] = date("Y-m-d H:i:s");
            $bool = db("serve")->where("id", $id)->update($status);
            if ($bool) {
                return ajax_success("更新成功",$status['reply']);
            } else {
                return ajax_error("更新失败");
            }
        }
    }



    /**
     * 售后详情
     * 陈绪
     */
    public function reply(Request $request){
        if($request->isPost()){
            $id = $request->only(["id"])["id"];
            $reply = db("serve")->where("id",$id)->field("reply")->find();
            return ajax_success("获取成功",$reply);
        }
    }



}