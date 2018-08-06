<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/2
 * Time: 10:23
 */
namespace  app\index\controller;

use think\Controller;
use think\Db;

class Register extends  Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 注册页面
     **************************************
     */
//        public function index()
//        {
//            if($_POST){
//                $data = input('post.');
//                if(empty($data['account'])){
//                    $this->error('用户名不能为空');
//                }
//                if(empty($data['passwd'])){
//                    $this->error('密码不能为空');
//                }
//                if(empty($data['yzm'])){
//                    $this->error('验证码不能为空');
//                }
//                $create_time =date('Y-m-d H:i:s');
//                $datas=[
//                    "user_name" => $data['account'],
//                    "password" => $data['passwd'],
//                    "create_time" =>$create_time,
//                    "status"=>1
//                ];
//                $name =Db::name('user')->field('user_name')->where('user_name',$data['account'])->find();
//                if(!empty($name)){
//                    $this->error("此用户名已被使用");
//                }
//                $res = Db::name('user')->insert($datas);
//                if(!$res && $res == null){
//                    $this->error("注册失败");
//                }
//                if($res)
//                {
//                    $this->success('注册成功',url('index/login/login'));
//                }
//            }
//                return view('index');
//        }
        public function index()
        {
            if($_POST){
                $data = input('post.');
                if(empty($data['account'])){
                    $this->error('用户名不能为空');
                }
                if(empty($data['passwd'])){
                    $this->error('密码不能为空');
                }
                if(empty($data['yzm'])){
                    $this->error('验证码不能为空');
                }
                $create_time =date('Y-m-d H:i:s');
                $datas=[
                    "user_name" => $data['account'],
                    "password" => $data['passwd'],
                    "create_time" =>$create_time,
                    "status"=>1
                ];
                $name =Db::name('user')->field('user_name')->where('user_name',$data['account'])->find();
                if(!empty($name)){
                    $this->error("此用户名已被使用");
                }
                $res = Db::name('user')->insert($datas);
                if(!$res && $res == null){
                    $this->error("注册失败");
                }
                if($res)
                {
                    $this->success('注册成功',url('index/login/login'));
                }
            }
                return view('index');
        }



}