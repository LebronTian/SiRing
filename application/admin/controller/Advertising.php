<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/19
 * Time: 14:03
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Advertising extends Controller{


    /**
     * 广告管理列表
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index(){
        return view("advertising_index");
    }



    /**
     * 广告图片
     * 陈绪
     */
    public function add(){

        return view("advertising_add");

    }


    /**
     * 广告图片添加入库
     * 陈绪
     */
    public function save(Request $request){
        $advertising_data = $request->param();
        $show_images = $request->file("images");
        $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'upload');
        $advertising_data["images"] = str_replace("\\", "/", $show_image->getSaveName());
        $bool = db("advertising")->insert($advertising_data);
        if($bool){
            $this->redirect(url("admin/Advertising/index"));
        }else{
            $this->error("入库错误",url("admin/Advertising/add"));
        }

    }

}