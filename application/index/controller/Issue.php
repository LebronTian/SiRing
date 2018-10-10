<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/19
 * Time: 10:15
 */

namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;

class Issue extends Controller{


    /**
     * 常见问题
     * 陈绪
     */

    public function index(Request $request){

        if($request->isPost()){
            $issue = db("issue")->select();
            return ajax_success("获取成功",$issue);
        }

        return view("issue_index");
    }


       //常见问题详情
    public function common_problem_details(Request $request){
       if($request->isPost()){
           $id = $request->only(["id"])["id"];
           $details = db("issue")->where("id",$id)->select();
           Session("details",$details);
           return ajax_success("获取成功");
       }
    }



    /**
     * 常见问题显示
     * 陈绪
     */
    public function details(Request $request){
        if($request->isPost()){
            $details = Session::get("details");
            return ajax_success("获取成功",$details);
        }

        return view('common_problem_details');
    }

    /**
     **************李火生*******************
     * 阿里鱼短信测试
     **************************************
     */
    public function sendSms($phone){
        $mobile = $phone;
        $code = mt_rand(10000, 99999);
        $result = sendMsg($mobile, $code);
        if ($result['Code'] == 'OK') {
            cache('tel' . $mobile, $code, 39);
            return json(['success' => 'ok', 'tel' => $mobile]);
        }
    }
    public function sendInformationByPhone(){
        $phone ='18998906797';
        $this->sendSms($phone);
    }




}