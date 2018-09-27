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
        if($_GET){
            $data = $_GET;
            $user_mobile =$data['account'];

            $password =$data['passwd'];

            if(empty($user_mobile)){
                $this->error('手机号不能为空');
            }
            if(empty($password)){
                $this->error('密码不能为空');
            }
            $res = Db::name('user')->field('password')->where('phone_num',$user_mobile)->find();
            if(!$res)
            {
                $this->error('手机号不存在');
            }
            $datas=[
                "phone_num" => $user_mobile,
                "password" => md5($password),
            ];
            $res =Db::name('user')->where($datas)->find();
           if(!$res && $res == null){
               $this->error("密码错误");
           }
           if($res){
               $res =Db::name('user')->where($datas)->where('status',1)->find();
               if($res)
               {
//                   $_SESSION['member'] =$datas;
                   session('member',$datas);
                   $this->success('登录成功',url('index/index/index'));
               }else{
                   $this->error('此用户已被管理员设置停用');
               }
           }
        }
        return view('login');
    }

    /**
     **************李火生*******************
     * 退出登录
     **************************************
     */
    public function logout(){
        Session('member',null);
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