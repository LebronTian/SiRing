<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25/025
 * Time: 14:13
 */

namespace app\admin\controller;

use think\console\Input;
use think\Controller;
use think\Db;
use think\Request;
use think\Image;

class Category extends Controller{


    /**
     * [商品分类显示]
     * 陈绪
     */
    public function index(){
        $category = db("goods_type")->where("status","<>","0")->select();
        $category_list = _tree_hTree(_tree_sort($category,"sort_number"));
        //halt($category_list);
        return view("category_index",["category"=>$category_list]);
    }

    /**
     * [商品分类添加]
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function add($pid = 0){
        $goods_cate = [];
        $goods_list = [];
        if($pid == 0){
            $goods_list = getSelectList("goods_type");
        }else{
            $goods_cate = db("goods_type")->where("id",$pid)->field()->select();
        }
        return view("category_add",["goods_list"=>$goods_list,"goods_cate"=>$goods_cate]);
    }


    /**
     * [商品分组入库]
     * 陈绪
     */
    public function save(Request $request){
        if($request->isPost()){
            $data = $request->param();
            $show_images = $request->file("type_images");
            if(!empty($show_images)){
                $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'type');
                $data["type_images"] = str_replace("\\","/",$show_image->getSaveName());
            }
            $type_images = $request->file("type_show_images");
            if(!empty($type_images)){
                $type_image = $type_images->move(ROOT_PATH . 'public' . DS . 'type');
                $data["type_show_images"] = str_replace("\\","/",$type_image->getSaveName());
            }
            $bool = db("goods_type")->insert($data);
            if($bool){
                $this->success("添加成功",url("admin/Category/index"));
            }else{
                $this->error("添加失败",url("admin/Category/add"));
            }
        }
    }




    /**
     * [商品分组显示]
     * 陈绪
     */
    public function ajax_id(Request $request){

        if($request->isPost()){
            $type_id = $request->only(["id"])['id'];
            $category = db("goods_type")->where("id",$type_id)->find();
            return ajax_success("获取成功",$category);
        }

    }



    /**
     * [商品分组修改]
     * [陈绪]
     */
    public function edit($pid=0,$id){
        $category = db("goods_type")->where("id",$id)->select();
        $category_name = db("goods_type")->where("id",$category[0]["pid"])->field("name,id")->select();
        if($pid == 0){
            $goods_list = getSelectList("goods_type");
        }
        return view("category_edit",["category"=>$category,"category_name"=>$category_name,"goods_lists"=>$goods_list]);
    }


    /**
     * [商品分组更新]
     * [陈绪]
     * @param Request $request
     * @param $id
     */
    public function updata(Request $request){
        if($request->isPost()) {
            $id = $request->only(["id"])["id"];
            $data = $request->only(["name", "status", "sort_number", "pid"]);
            $show_images = $request->file("type_images");
            if(!empty($show_images)){
                $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'type');
                $data["type_images"] = str_replace("\\","/",$show_image->getSaveName());
            }
            $type_images = $request->file("type_show_images");
            if(!empty($type_images)){
                $type_image = $type_images->move(ROOT_PATH . 'public' . DS . 'type');
                $data["type_show_images"] = str_replace("\\","/",$type_image->getSaveName());
            }

            $bool = db("goods_type")->where('id',$id)->update($data);
            if ($bool) {
                $this->success("编辑成功", url("admin/Category/index"));
            } else {
                $this->error("编辑失败", url("admin/Category/edit"));
            }
        }
    }


    /**
     * [商品分组删除]
     * [陈绪]
     */
    public function del($id){
        $bool = db("goods_type")->where("id",$id)->delete();
        if($bool){
            $this->success("删除成功",url("admin/Category/index"));
        }else{
            $this->error("删除失败",url("admin/Category/edit"));
        }
    }


    /**
     * [商品分组ajax显示]
     * 陈绪
     * @param int $pid
     * @return
     */
    public function ajax_add($pid = 0){
        $goods_list = [];
        if($pid == 0){
            $goods_list = getSelectList("goods_type");
        }
        return ajax_success("获取成功",$goods_list);
    }




    /**
     * 图片删除
     * 陈绪
     * @param Request $request
     * @return string|void
     */
    public function images(Request $request){
        if($request->isPost()) {
            $images = $request->only(['images'])['images'];
            $type_images = db("goods_type")->where("type_images",$images)->find();
            $show_images = db("goods_type")->where("type_show_images",$images)->find();

            if (!empty($type_images)){
                unlink(ROOT_PATH . 'public' . DS . 'type/' . $type_images['type_images']);
                db("goods_type")->where("type_images",$images)->update(["type_images"=>null]);
            }
            if (!empty($show_images)) {
                unlink(ROOT_PATH . 'public' . DS . 'type/' . $show_images['type_show_images']);
                db("goods_type")->where("type_show_images", $images)->update(['type_show_images'=>null]);
            }
            return ajax_success("删除成功");
        }

    }

}

