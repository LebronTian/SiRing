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

        }
        return view("category_add");
    }

}

