<?php

namespace app\index\controller;

use think\Controller;
class Index extends Controller
{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 首页信息
     **************************************
     */
    public function index()
    {
        return view("index");
    }



}
