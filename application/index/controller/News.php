<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/7
 * Time: 15:14
 */
namespace  app\index\controller;

use think\Controller;

class  News extends Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 商城资讯
     **************************************
     */
    public function  index(){
        return view("index");
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 新闻详情
     **************************************
     */
    public function  detail(){
        return view("detail");
    }

}