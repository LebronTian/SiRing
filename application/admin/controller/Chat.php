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
            $res_data =Db::name('chat')->where('who_say',1)->order('create_time','desc')->select();
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
            $res_data =Db::name('chat')->where('who_say',1)->where('status',1)->order("create_time","desc")->select();
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
            $res_data =Db::name('chat')->where('who_say',1)->where('status',0)->order("create_time","desc")->select();
            if(!empty($res_data)){
                return ajax_success('返回数据成功',$res_data);
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 聊天信息删除
     **************************************
     */
    public function chat_information_del(Request $request){
        if($request->isPost()){
            $chat_id = $request->only(['id'])['id'];
            if(!empty($cath_id)){
                $boll= Db::name('chat')->where('id',$chat_id)->delete();
                if($boll){
                    return ajax_success('删除成功',$chat_id);
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 在未读中按下回复按钮进入回复页面把状态值改变为已读
     **************************************
     */
    public function reading_information(Request $request){
        if($request->isPost()){
            $chat_id = $request->only(['id'])['id'];
            if(!empty($cath_id)){
                $boll= Db::name('chat')->where('id',$chat_id)->update(['status=>1']);
                if($boll){
                    return ajax_success('已读成功',$chat_id);
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 聊天界面
     **************************************
     */
    public function chat_window(){
        return view('chat_window');
    }



}