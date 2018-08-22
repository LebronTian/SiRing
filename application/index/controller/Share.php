<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/9
 * Time: 10:04
 */

namespace app\index\controller;

use think\Controller;
class Share extends Controller{


    /**
     * [晒单首页]
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function share_index(){
        return view("share_index");
    }


    /**
     * [晒单详情]
     * 陈绪
     */
    public function share_detail(){
        return view("share_detail");
    }
    public function evaluation(){
        return view("evaluation");
    }

}