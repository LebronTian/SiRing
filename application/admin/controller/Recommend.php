<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/16
 * Time: 10:09
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Recommend extends Controller{

    public function index(){

        $data = db("recommend")->select();
        return view("recommend_index",['data'=>$data]);

    }



    public function add(){
        return view("recommend_add");
    }



    public function save(Request $request){
        if($request->isPost()) {
            $data = $request->param();
            $images = $request->file("images");
            $image = $images->move(ROOT_PATH . 'public' . DS . 'uploads');
            $data["images"] = str_replace("\\", "/", $image->getSaveName());
            $bool = db("recommend")->insert($data);
            if ($bool) {
                return ajax_success("入库成功", $bool);
            } else {
                return ajax_error("入库失败");
            }
        }

    }



    public function edit($id){

        $data = db("recommend")->where("id",$id)->select();
        return view("recommend_edit",["data"=>$data]);

    }



    public function updata(Request $request){
        if($request->isPost()) {
            $data = $request->param();
            $id = $request->only(["id"])["id"];
            unset($data["id"]);
            $images = $request->file("images");
            if (!empty($images)) {
                $image = $images->move(ROOT_PATH . 'public' . DS . 'uploads');
                $data["images"] = str_replace("\\", "/", $image->getSaveName());
            }
            $bool = db("recommend")->where("id", $id)->update($data);
            if ($bool) {
                return ajax_success("入库成功", $bool);
            } else {
                return ajax_error("入库失败");
            }
        }

    }



    public function del($id){

        $bool = db("recommend")->where("id",$id)->delete();
        if($bool){
            $this->redirect("admin/Recommend/index");
        }

    }



    public function status(Request $request){

        $status = $request->only(["status"])["status"];
        $id = $request->only(["id"])["id"];
        $bool = db("recommend")->where("id",$id)->update(["status"=>$status]);
        if($bool){
            return ajax_success("更改成功",$bool);
        }else{
            return ajax_error("更改失败");
        }

    }




    public function images(Request $request){
        $id = $request->only(["id"])["id"];
        $data = db("recommend")->where("id",$id)->find();
        if(!empty($data)){
            unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $data['images']);
            $bool = db("recommend")->where("id",$id)->update(["images"=>null]);
            if($bool){
                return ajax_success("删除成功");
            }else{
                return ajax_error("删除失败");

            }
        }
    }



}