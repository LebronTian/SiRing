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
}