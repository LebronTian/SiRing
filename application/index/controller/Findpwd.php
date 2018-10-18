<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/6
 * Time: 10:20
 */
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Findpwd extends Controller{

    public function findPwd(){

        return view('findPwd');
    }
    /**
     **************李火生*******************
     * @param Request $request
     * 用来接收密码
     * phone_number 手机号
     * code     输入的验证码
     * password     输入的密码
     **************************************
     */
    public function find_password_by_phone(Request $request){
        if($request->isPost()){
            $mobile =trim($_POST['phone_number']);
            $code =trim($_POST['code']);
            $password =trim($_POST['password']);
            $data=[
                'phone_number'=>$mobile,
                'password'=>$password,
                'code'=>$code,
            ];
            if(!empty($data)){
//                if (strlen($mobile) != 11 || substr($mobile, 0, 1) != '1' ) {
//                    return ajax_success("请输入正确的手机号码",$mobile);
//                }

//                if($code == ''){
//                   return ajax_success('验证码不能为空',$code);
//                }
               $res =Db::name('user')->field('phone_num')->where('phone_num',$mobile)->select();
                if(empty($res)){
                    return ajax_success('此手机号不能存在，请前往注册',$mobile);
                }else{

                    if (session('mobileCode') != $code) {
                            return ajax_success("验证码不正确",$mobile);
                    }else{
                        return ajax_success('chwengg',$mobile);
//                                $password_bool =Db::name('user')->where('phone_num',$mobile)->update(['password'=>$password]);
//    //                            if(!empty($password_bool)){
//    //                                $user_data =Db::name('user')->where('phone_num',$mobile)->find();
//    //                                return ajax_success('密码修改成功',$user_data);
//    //                            }else{
//    //                            return ajax_success('密码修改失败',$password_bool);
//    //                        }
//                        return ajax_success('密码修改成功',$password_bool);
                        }
                }
            }

        }
    }


    public function sendMobileCodeByPhone(Request $request)
    {
        //接受验证码的手机号码
        if ($request->isPost()) {
            $mobile = $_POST["mobile"];
            $mobileCode = rand(100000, 999999);
            $arr = json_decode($mobile, true);
            $mobiles = strlen($arr);
            if (isset($mobiles) != 11) {
                return ajax_error("手机号码不正确");
            }
            //存入session中
            if (strlen($mobileCode)> 0){
                session('mobileCode',$mobileCode);
                $_SESSION['mobile'] = $mobile;
            }
            $output = sendMsg($mobile, $mobileCode);
            if ($output) {
                return ajax_success("发送成功", $output);
            } else {
                return ajax_error("发送失败",$output);
            }
        }
    }
}