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
        return view("electron_index");

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

}