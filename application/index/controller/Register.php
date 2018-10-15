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
use think\Request;

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
            $url = $_SERVER['REQUEST_URI'];
            $number = strrchr($url,'=');
            $numbers = substr($number,1);
            return view('index',['number'=>$numbers]);
        }


    /**
     **************李火生*******************
     * @param Request $request
     * 手机注册
     **************************************
     */
        public function  doRegByPhone(Request $request){
            if($request->isPost())
            {
                $mobile = trim($_POST['mobile']);
                $code = trim($_POST['mobile_code']);
                $password =trim($_POST['password']);
                $confirm_password =trim($_POST['confirm_password']);
                $create_time =date('Y-m-d H:i:s');
                if($password !==$confirm_password ){
                    return ajax_error('两次密码不相同');
                }
                if (strlen($mobile) != 11 || substr($mobile, 0, 1) != '1' || $code == '') {
                    return ajax_error("参数不正确");
                }
                if (session('mobileCode') != $code) {
                    return ajax_error("验证码不正确");
                }
                if (session('mobileCode') == $code) {
                    $data =[
                        'phone_num'=>$mobile,
                        'password'=>md5($password),
                        'create_time'=>strtotime($create_time),
                        "status"=>1
                    ];
                    $invitation = $request->only(['invitation'])['invitation'];
                    $res =Db::name('user')->insert($data);
                    if(!empty($invitation)) {
                        $data = [];
                        $id = substr($invitation,3);
                        $data['user_id'] = $id;
                        $data['invitation'] = $invitation;
                        $bool = db("discounts_user")->insert($data);
                        if($bool){
                            return ajax_success('注册成功',$data);
//                        $this->success('注册成功','index/login/login');
                        }
                    }else if($res){
                        return ajax_success('注册成功',$data);
//                    $this->success('注册成功','index/login/login');
                    }
                }



            }
        }

    /**
     **************李火生*******************
     * @param Request $request
     * 邮箱注册
     **************************************
     */
        public function  doRegByEmail(Request $request){
            if($request->isPost())
            {
                dump($_POST);exit();
            }
        }

    /**
     **************李火生*******************
     * @param Request $request
     * 手机验证码
     **************************************
     */
        public function sendMobileCode(Request $request)
        {
            //接受验证码的手机号码
            if ($request->isPost()) {
                $mobile = $_POST["mobile"];
              $res =  Db::name('user')->field('phone_num')->where('phone_num',$mobile)->select();
              if($res){
                  return ajax_success('此手机号已经注册',$mobile);
              }
                $mobileCode = rand(100000, 999999);
                $arr = json_decode($mobile, true);
                $mobiles = strlen($arr);
                if (isset($mobiles) != 11) {
                    return ajax_success("手机号码不正确",$mobile);
                }
                //存入session中
                if (strlen($mobileCode)> 0) {
                    session('mobileCode',$mobileCode);
                    $_SESSION['mobile'] = $mobile;
                }
//                $content = "尊敬的用户，您本次验证码为{$mobileCode}，十分钟内有效";
//                $url = "http://120.26.38.54:8000/interface/smssend.aspx";
//                $post_data = array("account" => "gagaliang", "password" => "123qwe", "mobile" => "$mobile", "content" => $content);
//                $ch = curl_init();
//                curl_setopt($ch, CURLOPT_URL, $url);
//                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//                curl_setopt($ch, CURLOPT_POST, 1);
//                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
//                $output = curl_exec($ch);
//                curl_close($ch);
                $output = sendMsg($mobile, $mobileCode);
                if ($output) {
                   return ajax_success("发送成功", $output);
                } else {
                    return ajax_success("发送失败",$output);
                }
            }
        }



}