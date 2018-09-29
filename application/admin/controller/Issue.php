<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/13
 * Time: 16:53
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Issue extends Controller{

    public $status = [0,1];

    /**
     * 常见问题
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){
        $issue = db("issue")->select();
        return view("issue_index",["issue"=>$issue]);

    }


    /**
     * 常见问题添加
     * 陈绪
     */
    public function add(){

        return view("issue_add");

    }


    /**
     * 常见问题入库
     * 陈绪
     */
    public function save(Request $request){
        $issue_data = $request->param();
        $issue_data["status"] = $this->status[1];
        $issue_data["create_time"] = time();
        $bool = db("issue")->insert($issue_data);
        if($bool){
            $this->redirect(url("admin/Issue/index"));
        }else{
            $this->error("添加失败",url("admin/Issue/add"));
        }
    }



    /**
     * 常见问题修改
     * 陈绪
     */
    public function edit($id){
        $issue = db("issue")->where("id",$id)->select();
        return view("issue_edit",["issue"=>$issue]);
    }



    /**
     * 常见问题删除
     * 陈绪
     */
    public function del($id){
        $bool = db("issue")->where("id",$id)->delete();
        if($bool){
            $this->redirect(url("admin/issue/index"));
        }else{
            $this->error("添加失败",url("admin/Issue/index"));
        }
    }


    /**
     * 常见问题更新
     * 陈绪
     */
    public function updata(Request $request){
        $id = $request->only(['id'])['id'];
        $issue_data = $request->except("id");
        $bool = db("issue")->where("id",$id)->update($issue_data);
        if($bool){
            $this->redirect(url("admin/Issue/index"));
        }else{
            $this->error("添加失败",url("admin/Issue/edit"));
        }
    }



    /**
     * [常见问题不显示]
     * 陈绪
     */
    public function status(Request $request){
        if ($request->isPost()){
            $issue_id = $request->only(['id'])['id'];
            $status["status"] = $this->status[0];
            $bool = db("issue")->where("id",$issue_id)->update($status);
            if ($bool){
                return ajax_success("更新成功");
            }else{
                return ajax_error("更新失败");
            }
        }

    }



    /**
     * [常见问题显示]
     * 陈绪
     * @param Request $request
     * @return
     */
    public function putaway(Request $request){
        if ($request->isPost()){
            $issue_id = $request->only(['id'])['id'];
            $status["status"] = $this->status[1];
            $bool = db("issue")->where("id",$issue_id)->update($status);
            if ($bool){
                return ajax_success("更新成功");
            }else{
                return ajax_error("更新失败");
            }
        }
    }





}