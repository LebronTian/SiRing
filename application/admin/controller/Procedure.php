<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/13
 * Time: 14:58
 */

namespace app\admin\controller;
use think\Controller;

class Procedure extends Controller{

    /**
     * 小程序首页
     * 陈绪
     */
    public function index(){

        return view("procedure_index");

    }

}