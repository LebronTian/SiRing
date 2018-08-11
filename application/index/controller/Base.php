<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/11
 * Time: 17:05
 */
namespace  app\index\controller;


use think\Controller;

class  Base extends  Controller{

//TODO:基类,防止直接敲代码直接登录
    public   $account;

    //初始化函数
    public function _initialize()
    {
        //检测登录情况,防止直接敲代码直接登录
        if(!$this->isLogin())
        {
            $this->redirect('index/Login/login');
        }
    }
    public function isLogin()
    {
        $login_user =$this->getLoaginUser();
        if(!$login_user)
        {
            return false;
        }
        return true;
    }
    public function  getLoaginUser()
    {
        //懒加载
        if(!$this->account)
        {
            $this->account =session('member');
        }
        return $this->account;
    }

}