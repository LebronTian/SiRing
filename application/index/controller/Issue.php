<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/19
 * Time: 10:15
 */

namespace app\index\controller;
use think\Controller;

class Issue extends Controller{


    /**
     * 常见问题
     * 陈绪
     */

    public function index(){

        $issue = db("issue")->select();
        return view("issue_index");
    }

}