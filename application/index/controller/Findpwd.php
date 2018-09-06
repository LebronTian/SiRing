<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/6
 * Time: 10:20
 */
namespace app\index\controller;

use think\Controller;
use think\Request;

class Findpwd extends Controller{

    public function findPwd(){

        return view('findPwd');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 用来接收密码
     **************************************
     */
    public function find_password_by_phone(Request $request){
        if($request->isPost()){
            $data_phone =$_POST['u_name'];
            dump($data_phone);exit();
        }
    }
}