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
//        if($_POST){
//            $data = input('post.');
//            if(empty($data['account'])){
//                $this->error('用户名不能为空');
//            }
//            if(empty($data['passwd'])){
//                $this->error('密码不能为空');
//            }
//            if(empty($data['yzm'])){
//                $this->error('验证码不能为空');
//            }
//            $create_time =date('Y-m-d H:i:s');
//            $datas=[
//                "user_name" => $data['account'],
//                "password" => $data['passwd'],
//                "create_time" =>$create_time,
//                "status"=>1
//            ];
////            $name =Db::name('user')->field('user_name')->select();
//           $res = Db::name('user')->insert($datas);
//           if(!$res && $res == null){
//               $this->error("注册失败");
//           }
//           if($res)
//           {
//               $this->success('注册成功',url('index/index/login'));
//           }
//
//
//
//        }



        return view('login');
    }

    /**
     **************李火生*******************
     * 退出登录
     **************************************
     */
    public function loginout(){

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