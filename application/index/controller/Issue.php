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
               return ajax_success("获取成功",$details);
           }
        return view('common_problem_details');
    }




}