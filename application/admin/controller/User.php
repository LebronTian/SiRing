<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/3
 * Time: 10:40
 */
namespace app\admin\controller;

use think\Controller;

class User extends Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员概况
     **************************************
     */
    public function index(){
        return view('user_index');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员等级
     **************************************
     */
    public function grade(){
        return view('user_grade');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员等级规则
     **************************************
     */
    public function rule(){
        return view('user_rule');
    }



}