<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/28
 * Time: 15:15
 */
namespace  app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class  Chat extends  Base {


    /**
     **************李火生*******************
     * @return \think\response\View
     * 显示聊天信息
     **************************************
     */
    public function  chat(){
        return view('chat');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 用户发送信息
     **************************************
     */
    public function chat_push(Request $request){
        if($request->isPost()){
            $data_message =$request->only(['message'])['message'];
            if(!empty($data_message)){
                $member =Session::get('member');
                if(!empty($member)) {
                    $user_data = Db::name('user')->field('id,user_name,phone_num')->where('phone_num', $member['phone_num'])->find();
                    if(!empty($user_data)){
                        $data =[
                            'user_name'=>$user_data['user_name'],
                            'user_id'=>$user_data['id'],
                            'user_phone'=>$user_data['phone_num'],
                            'chat_content'=>$data_message,
                            'create_time'=>time(),
                            'who_say'=>1
                        ];
                        $res =Db::name('chat')->data($data)->insert();
                        return ajax_success('发送成功',$res);
                    }
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 接收客服发送的聊天信息
     **************************************
     */
    public function chat_pull(Request $request){
        if($request->isPost()){
            $member =Session::get('member');
            $member_id =Db::name('user')->field('id')->where('phone_num',$member['phone_num'])->find();
           if(!empty($member_id)){
               $all_chat_data=Db::name('chat')->where('user_id',$member_id['id'])->order('create_time','asc')->select();
               if(!empty($all_chat_data)){
                   return ajax_success('接收成功',$all_chat_data);
               }
           }


        }
    }

}