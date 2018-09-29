<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/29
 * Time: 11:46
 */
namespace app\admin\controller;
use think\Controller;
use think\Paginator;
use think\Request;

class Electron extends Controller{

    /**
     * 电子保修卡
     * 陈绪
     */
    public function index(Request $request){
        $data = db("electron")->select();
        return view("electron_index",["data"=>$data]);

    }


    /**
     * 电子保修卡
     * 陈绪
     */
    public function add(){
        return view("electron_add");

    }


    /**
     * 电子保修卡添加
     * 陈绪
     */
    public function save(Request $request){
        $data = $request->param();
        $bool = db("electron")->insert($data);
        if($bool){
            $this->success("添加成功");
        }
    }



    public function del($id){
        $bool = db("electron")->where("id",$id)->delete();
        if($bool){
            $this->success("删除成功",url("admin/Electron/index"));
        }
    }

}