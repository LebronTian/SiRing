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
        $advertising_data = db("advertising")->select();
        return view("advertising_index",["advertising"=>$advertising_data]);
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



    /**
     * 广告图片修改
     * 陈绪
     */
    public function edit($id){
        $data = db("advertising")->where("id",$id)->select();
        return view("advertising_edit",['data'=>$data]);
    }


    /**
     * 更新
     * 陈绪
     * @param Request $request
     */
    public function updata(Request $request){
        $id = $request->only(['id'])['id'];
        $show_images = $request->file("images");
        $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'upload');
        $advertising_data["images"] = str_replace("\\", "/", $show_image->getSaveName());
        $bool = db("advertising")->where("id",$id)->update($advertising_data);
        if($bool){
            $this->redirect(url("admin/Advertising/index"));
        }else{
            $this->error("入库错误",url("admin/Advertising/edit"));
        }
    }



    /**
     * 图片删除
     * 陈绪
     * @param Request $request
     * @return string|void
     */
    public function images(Request $request){
        $id = $request->only(['id'])['id'];
        $images = db("advertising")->where("id",$id)->field("images")->find();
        $bool = db("advertising")->where("id",$id)->update(['images'=>null]);
        unlink(ROOT_PATH . 'public' . DS . 'upload/'.$images['images']);
        if($bool){
            return ajax_success("更新成功");
        }
    }



    /**
     * 删除
     * 陈绪
     */
    public function del($id){
        $images = db("advertising")->where("id",$id)->field("images")->find();
        unlink(ROOT_PATH . 'public' . DS . 'upload/'.$images['images']);
        $bool = db("advertising")->where("id",$id)->delete();
        if($bool){
            return ajax_success("更新成功");
        }
    }

}