<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/9
 * Time: 10:04
 */

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;

class Share extends Controller{


    /**
     * [晒单首页]
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function share_index(){
        return view("share_index");
    }


    /**
     * [晒单详情]
     * 陈绪
     */
    public function share_detail(){
        return view("share_detail");
    }

    /**
     **************李火生*******************
     * @return \think\response\View
     * 追加评价
     **************************************
     */
    public function evaluation(){
       $evaluation_order_id = Session::get('evaluation_order_id');
       if(!empty($evaluation_order_id)){
           $res = Db::name('order')->where('id',$evaluation_order_id)->find();
           $this->assign('res',$res);
       }
        return view("evaluation");
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 发表评价
     **************************************
     */
    public function  evaluation_add(Request $request){
        if($request->isPost()){
            $evaluation_order_id = Session::get('evaluation_order_id');
            if(!empty($evaluation_order_id)){
                $data =$_POST;
                if(!empty($data)){
                    return ajax_success('成功',$data);
                }
            }
        }
    }

    public  function  evaluation_add_img(Request $request){
        if($request->isPost()){
            $file = request()->file('goods_images');
            dump($file);exit();
        }
    }



    /**
     **************李火生*******************
     * @param Request $request
     * 获取需要评价的order_id
     **************************************
     */
    public function evaluation_get_order_id(Request $request){
        if($request->isPost()){
            $order_id =$request->only(["order_id"])['order_id'];
            if(!empty($order_id)){
                    session('evaluation_order_id',$order_id);
                    return ajax_success('成功',$order_id);
            }
        }
    }

    public function  evaluation_use(){
        return view('evaluation_use');
    }


}