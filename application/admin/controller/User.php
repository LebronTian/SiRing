<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/3
 * Time: 10:40
 */
namespace app\admin\controller;

use think\Controller;
use  think\Db;
use think\Request;

class User extends Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员概况
     **************************************
     */
    public function index(Request $request){
       $data = Db::name('user')->field('id ,user_name,password,sex,phone_num,email,city,create_time,status')->order('id desc')->select();
       if($request->isPost()){
        return ajax_success('成功',$data);
       }
        return view('user_index');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员增加
     **************************************
     */
    public function add(){
        return view("user_add");
    }

    /**
     **************李火生*******************
     * 用户信息添加功能实现
     **************************************
     */
    public function save(){
        if($_POST){
            $data =input('post.');
            $username =$data['username'];
            if(empty($username)){
                $this->error('用户名不能为空',url('admin/user/add'));
            }
            $password =$data['password'];
            if(empty($password)){
                $this->error('密码不能为空',url('admin/user/add'));
            }
            $sex =$data['sex'];
            if(empty( $sex)){
                $this->error('性别不能为空',url('admin/user/add'));
            }
            $phone =$data['phone'];
            if(empty($phone)){
                $this->error('电话号码不能为空',url('admin/user/add'));
            }
            $email =$data['email'];
            if(empty($email)){
                $this->error('邮箱不能为空',url('admin/user/add'));
            }
            //判断数据库中是否已经注册
            $res_name =Db::name('user')->where('user_name',$username)->find();
            if($res_name){
                $this->error('此用户名已被注册',url('admin/user/add'));
            }
            $res_phone =Db::name('user')->where('phone_num',$phone)->find();
            if($res_phone){
                $this->error('此手机号已注册',url('admin/user/add'));
            }
            $res_email =Db::name('user')->where('email',$email)->find();
            if($res_email){
                $this->error('此邮箱已注册',url('admin/user/add'));
            }
            $datas =[
              'user_name'=>$data['username'],
                'password'=>md5($data['password']),
                'sex'=>$data['sex'],
                'phone_num'=>$data['phone'],
                'email'=>$data['email'],
                'city'=>$data['city'],
                'create_time'=>date('Y-m-d H:i:s'),
                'remark'=>$data['remark'],
                'status' => 1
            ];
          $res =  Db::name('user')->insert($datas);
          if($res){
//              $this->success('会员添加成功');
              $this->success('会员添加成功',url('admin/user/index'));
          }else{
              $this->error('会员添加失败');
          }

        }
    }


    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员删除
     **************************************
     */
    public function del(Request $request){
        if($request->isPost('id')){
            $id  =$_POST['id'];
          $res =  Db::name('user')->where('id',$id)->delete();
          if($res)
          {
              $this->success('成功',url('admin/user/index'));
          }
        }
    }

    /**
     **************李火生*******************
     * 软删除，修改状态值
     **************************************
     */
    public function status(Request $request){
      if($request->isPost()){
          $id =$_POST['id'];
          $status =Db::name('user')->field('status')->where('id',$id)->find();
          $statu =$status['status'];
          if($statu ==1){
              $res =Db::name('user')->where('id',$id)->update(['status'=>0]);
              if($res){
                  $this->success('成功',url('admin/user/index'));
              }
          }
          if($statu ==0){
              $res =Db::name('user')->where('id',$id)->update(['status'=>1]);
              if($res){
                  $this->success('成功',url('admin/user/index'));
              }
          }
      }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 状态启用
     **************************************
     */
    public function statu(Request $request){
        if($request->isPost()){
            $id =$_POST['id'];
            $status =Db::name('user')->field('status')->where('id',$id)->find();
            $statu =$status['status'];
            if($statu ==0){
                $res =Db::name('user')->where('id',$id)->update(['status'=>1]);
                if($res){
                    $this->success('成功',url('admin/user/index'));
                }
            }

        }
    }




    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员编辑
     **************************************
     */
    public function edit(Request $request){

        if($request->isPost()){
            $id =$_POST['id'];
            $_SESSION['id'] =$id;
            $datas = Db::name('user')->where('id',$id)->find();
              return ajax_success('成功',$datas);
        }
        return view("user_edit");
    }

    public function edits(Request $request){
        if($request->isPost()){
        $id =$_SESSION['id'];
        $datas = Db::name('user')->where('id',$id)->find();
            unset($_SESSION['id']);
            return ajax_success('成功',$datas);
        }
    }


    public function  update(Request $request){
        if($request->isPost()){

        }
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员展示信息
     **************************************
     */
    public function show(){
        return view("user_show");
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     *会员密码修改
     **************************************
     */
    public function pass_edit(){
        return view("pass_edit");
    }





    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员等级
     **************************************
     */
    public function grade(){
        return view('user_grade');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员等级规则
     **************************************
     */
    public function rule(){
        return view('user_rule');
    }



}