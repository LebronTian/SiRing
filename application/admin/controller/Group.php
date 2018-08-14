<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/13
 * Time: 14:51
 */

namespace app\admin\controller;
use think\Controller;

class Group extends Controller{

    /**
     * 拼团
     * 陈绪
     */
    public function index(){

        return view("group_index");

    }

}