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
     * 会员增加
     **************************************
     */
    public function add(){
        return view("user_add");
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员删除
     **************************************
     */
    public function del(){
        return view("user_add");
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员编辑
     **************************************
     */
    public function edit(){
        return view("user_edit");
    }

    public function show(){
        return view("user_show");
    }

    public function pass_edit(){
        return view("pass_edit");
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