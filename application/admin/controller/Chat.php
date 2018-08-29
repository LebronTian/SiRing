<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/29
 * Time: 11:19
 */
namespace  app\admin\controller;
use think\Controller;
use think\Request;
use think\Db;

class  Chat extends Controller{

    /**
     **************李火生*******************
     * @return \think\response\View
     * 聊天的首页信息
     **************************************
     */
    public function index(){
        return view('index');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 后台获取用户发送过来的聊天信息
     **************************************
     */
    public function all_information(Request $request){
        if($request->isPost()){
            $res_data =Db::name('chat')->where('who_say',1)->select();
            if(!empty($res_data)){
                return ajax_success('返回数据成功',$res_data);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 后台获取用户发送过来的聊天信息（已读）
     **************************************
     */
    public function read_all_information(Request $request){
        if($request->isPost()){
            $res_data =Db::name('chat')->where('who_say',1)->where('status',1)->select();
            if(!empty($res_data)){
                return ajax_success('返回数据成功',$res_data);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 后台获取用户发送过来的聊天信息（未读）
     **************************************
     */
    public function unread_all_information(Request $request){
        if($request->isPost()){
            $res_data =Db::name('chat')->where('who_say',1)->where('status',0)->select();
            if(!empty($res_data)){
                return ajax_success('返回数据成功',$res_data);
            }
        }
    }



}