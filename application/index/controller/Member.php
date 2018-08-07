<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/7
 * Time: 15:05
 */
namespace  app\index\controller;

use think\Controller;

class  Member extends  Controller{
    public  function  index(){
        return view('member_index');
    }
}