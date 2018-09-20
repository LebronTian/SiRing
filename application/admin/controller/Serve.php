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



}