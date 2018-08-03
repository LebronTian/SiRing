<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25/025
 * Time: 14:13
 */

namespace app\admin\controller;
use think\Controller;
use think\Request;

class Category extends Controller{

    /**
     * [商品分类显示]
     * 陈绪
     */
    public function index(){
        return view("category_index");
    }

    /**
     * [商品分类添加]
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function add(Request $request){
        if($request->isPost()){
            $categroy_name = getSelectList("goods_type");
            return ajax_success("获取成功",$categroy_name);
        }
        return view("category_add");
    }


    /**
     * [商品入库]
     * 陈绪
     * @param Request $request
     * @return mixed|string
     */
    public function save(Request $request){

        if($request->isPost()){
            $data = $request->param();
            $bool = db("goods_type")->insert($data);
            if ($bool){
                return ajax_success("入库成功");
            }else{
                return ajax_error("入库失败");
            }

        }
    }

}

