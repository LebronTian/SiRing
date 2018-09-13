<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/13
 * Time: 10:40
 */

namespace app\admin\controller;
use think\Controller;
use think\Request;

class Content extends Controller{


    /**
     * 电子协议列表
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){
        $content = db("contract")->select();
        return view("content_index",["content"=>$content]);
    }



    /**
     *电子协议添加
     *陈绪
     */
    public function add(){
        return view("content_add");
    }


    /**
     * 电子协议添加
     * 陈绪
     */
    public function save(Request $request){
        $content_data = $request->param();
        $bool = db("contract")->insert($content_data);
        if($bool){
            $this->redirect(url("admin/Content/index"));
        }else{
            $this->error("添加失败",url("admin/Content/add"));
        }
    }



    /**
     * 电子协议修改
     * 陈绪
     */
    public function edit($id){
        $content = db("contract")->where("id",$id)->select();
        return view("content_edit",["content"=>$content]);
    }



    /**
     * 电子协议删除
     * 陈绪
     */
    public function del($id){
        $bool = db("contract")->where("id",$id)->delete();
        if($bool){
            $this->redirect(url("admin/Content/index"));
        }else{
            $this->error("添加失败",url("admin/Content/index"));
        }
    }


    /**
     * 电子协议更新
     * 陈绪
     */
    public function updata(Request $request){
        $id = $request->only(['id'])['id'];
        $content_data = $request->except("id");
        $bool = db("contract")->where("id",$id)->update($content_data);
        if($bool){
            $this->redirect(url("admin/Content/index"));
        }else{
            $this->error("添加失败",url("admin/Content/edit"));
        }
    }
}