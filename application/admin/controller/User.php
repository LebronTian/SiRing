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
     * 搜索功能
     **************************************
     */
    public function search(Request $request){
        if($request->isPost()){
            $keywords =input('search_key');
            $timemin  =strtotime(input('datemin'));
            $timemax  =strtotime(input('datemax'));
            if(empty($timemin)||empty($timemax)){
                $condition = " `email` like '%{$keywords}%' or `user_name` like '%{$keywords}%' or `phone_num` like '%{$keywords}%' ";
                $res = Db::name("user")->where($condition)->select();
                return ajax_success('成功',$res);
            }
            if(!empty($timemin)&&!empty($timemax)){
                if(empty($keywords)){
                    $condition = "create_time>{$timemin} and create_time< {$timemax}";
                    $res = Db::name("user")->where($condition)->select();
                    return ajax_success('成功',$res);

                }
                if(!empty($keywords)){
                    $condition = " `email` like '%{$keywords}%' or `user_name` like '%{$keywords}%' or `phone_num` like '%{$keywords}%' ";
                    $res = Db::name("user")->where($condition)->select();
                    return ajax_success('成功',$res);
                }

            }




        }



//        return view('user_index');
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员增加
     **************************************
     */
    public function add(){
        $province = Db::name('tree')->where (array('pid'=>1) )->select();
        $this->assign('province',$province);
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
            if (strlen($phone)!=11)
            {
                $this->error('手机号码应为11位数');
            }
            if(!is_numeric($phone)){
                $this->error('手机号码必须为为数字');
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
//           $city =implode(',',$data['city']);
            $city =$data['city'];
            $province_id =$data['province_id'];
            $city_id =$data['city_id'];
            $town_id =$data['town_id'];
            $time = date('Y-m-d H:i:s');
            $times =strtotime($time);
            $datas =[
              'user_name'=>$data['username'],
                'password'=>md5($data['password']),
                'sex'=>$data['sex'],
                'phone_num'=>$data['phone'],
                'email'=>$data['email'],
                'city'=>$city,
                'province_id'=>$province_id,
                'city_id'=>$city_id,
                'town_id'=>$town_id,
                'create_time'=> $times,
                'remark'=>$data['remark'],
                'status' => 1
            ];
          $res =  Db::name('user')->insert($datas);
          if($res){
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
     * @param Request $request
     * 批量删除
     **************************************
     */
    public function dels(Request $request){
        if($request->isPost()){
            $id =$_POST['id'];
            if(is_array($id)){
                $where ='id in('.implode(',',$id).')';
            }else{
                $where ='id='.$id;
            }
            $list =  Db::name('user')->where($where)->delete();
            if($list!==false)
            {
                $this->success('成功删除!');
            }else{
                $this->error('删除失败');
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

    /**
     **************李火生*******************
     * @param Request $request
     * 会员编辑页面获取传值
     **************************************
     */
    public function edits(Request $request){
        if($request->isPost()){
        $id =$_SESSION['id'];
        $datas = Db::name('user')->where('id',$id)->find();
//            unset($_SESSION['id']);
            return ajax_success('成功',$datas);
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 会员信息编辑更新
     **************************************
     */
    public function  update(Request $request){
        if($request->isPost()){
            $id =$_SESSION['id'];
            $tb_data =Db::name('user')->where('id',$id)->find();
            $user_name = $_POST['user_name'];
            $phone =$_POST['phone_num'];
            $email =$_POST['email'];
            $exist_info = Db::name('user')->field('user_name,phone_num,email')->select();
             foreach ($exist_info as $k=>$v){
                if($user_name ==$v['user_name'] && $user_name !=$tb_data['user_name']){
                     $this->error('用户名不能改为已存在的');
                 }
                 if($phone ==$v['phone_num'] &&$phone !=$tb_data['phone_num']){
                     $this->error('手机号不能改为已存在的');
                 }
                 if($phone ==$v['email'] &&$phone !=$tb_data['email']){
                     $this->error('邮箱不能改为已存在的');
                 }
             }
                $data =[
                    'user_name'=>$user_name,
                    'sex'=>$_POST['sex'],
                    'phone_num'=>$phone,
                    'email'=>$email,
                    'city'=>$_POST['city'],
                    'province_id'=>$_POST['province_id'],
                    'city_id'=>$_POST['city_id'],
                    'town_id'=>$_POST['town_id'],
                    'remark'=>$_POST['remark']
                ];
                if(!empty($data)){
                    $res =Db::name('user')->data($data)->where('id',$id)->update();
                    if($res){
                        $this->success('更新成功',url('admin/user/index'));
                        unset($_SESSION['id']);
                    }
                    if(!$res){
                       $this->error('更新失败');
                        unset($_SESSION['id']);
                    }
                }


        }
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 会员展示信息
     **************************************
     */
    public function show(Request $request){
        if($request->isPost()){
            $id = $_POST['id'];
            $_SESSION['user_id'] =$id;
        }
        return view("user_show");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 用来传值回去使用
     **************************************
     */
    public function  shows(Request $request){
        if($request->isPost())
        {
            $id =$_SESSION['user_id'];
            $res = Db::name('user')->where('id',$id)->find();
            if($res){
                return ajax_success('成功',$res);
            }else{
                return ajax_error('失败');
            }
        }
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
     * 城市测试
     **************************************
     */
    public function cityss(){
        return view('user_cityss');
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

    public function getRegion(){
        $Region=Db::name("tree");
        $map['pid']=$_REQUEST["pid"];
        $map['type']=$_REQUEST["type"];
        $list=$Region->where($map)->select();
        echo json_encode($list);
    }





}