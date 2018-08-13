<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/6
 * Time: 10:20
 */
namespace app\index\controller;

use think\Controller;

class Findpwd extends Controller{

    public function findPwd(){

        return view('findPwd');
    }
}