<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/11
 * Time: 17:05
 */
namespace  app\index\controller;


use think\Controller;

class  SelfService extends  Controller{
    // 自助服务 首页
    public function index(){
        return view('index');
    }
    // 售后维修
    public function repair(){
        return view('repair');
    }
    // 问题描述
    public function repair_desc(){
        return view('repair_desc');
    }
    // 提交成功
     public function successful_sub(){
        return view('successful_sub');
    }
    // 处理中
    public function processing(){
        return view('processing');
    }
    // 评价
    public function evaluation(){
        return view('evaluation');
    }
    // 服务单详情
    public function detail_info(){
        return view('detail_info');
    }
}