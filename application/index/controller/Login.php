<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/2
 * Time: 9:38
 */
namespace app\index\controller;

use think\Controller;
use think\captcha\Captcha;
use think\Db;
use think\Request;

class Login extends Controller{

    /**
     **************李火生*******************
     * @return \think\response\View
     * 登录
     **************************************
     */
    public function login()
    {
        if($_POST){
            $data = $_POST;
            $user_name =$data['account'];

            $password =$data['passwd'];

            if(empty($user_name)){
                $this->error('用户名不能为空');
            }
            if(empty($password)){
                $this->error('密码不能为空');
            }
            $res = Db::name('user')->field('password')->where('user_name',$user_name)->find();
            if(!$res)
            {
                $this->error('用户名不存在');
            }
            $datas=[
                "user_name" => $user_name,
                "password" => md5($password),
            ];
            $res =Db::name('user')->where($datas)->find();
           if(!$res && $res == null){
               $this->error("密码错误");
           }
           if($res)
           {
               $_SESSION['user'] =$datas;
               $this->success('登录成功',url('index/index/index'));
           }
        }
        return view('login');
    }

    /**
     **************李火生*******************
     * 退出登录
     **************************************
     */
    public function loginout(){
        unset($_SESSION['member']);
        $this->success('退出成功',url('index/Login/login'));
    }
    /**
     **************李火生*******************
     * 验证码
     **************************************
     */
    public function captchas(){
        $captcha = new Captcha([
            'imageW'=>100,
            'imageH'=>48,
            'fontSize'=>18,
            'useNoise'=>false,
            'useCurve' =>false,
            'length'=>3,
        ]);
        return $captcha->entry();
    }


}