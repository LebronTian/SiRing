<?php
/**
 * Created by PhpStorm.
 * User: CHEN
 * Date: 2018/7/11
 * Time: 16:12
 */

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Goods extends Controller{
    /**
     * [商品列表]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     * 陈绪
     */
    public function index(){
        return view("goods_index");
    }

    public function add(){

        return view("goods_add");
    }

    public function save(Request $request){
       if ($request->isPost()){
           $goods_data = $request->only([
                        "goods_name",
                        "sort_number",
                        "goods_type_id",
                        "goods_specification",
                        "goods_place",
                        "goods_supplier",
                        "goods_unit",
                        "goods_bazaar_money",
                        "goods_bottom_money",
                        "goods_keyword",
                        "goods_abstract",
                        "goods_detail",
           ]);
           $bool = db("goods")->insert($goods_data);
           if($bool){
               //取出图片在存到数据库
               $goods_images = $request->only(["goods_images"])["goods_images"];
               if(!empty($goods_images)){
                   $dir_name = "/public/static/admin/upload".date("Y-m-d");
                   if(is_dir($dir_name)) {
                       mkdir($dir_name, 777);
                   }

                   $strrchr = uniqid().time().strstr($goods_images,"d",true);
                   halt($strrchr);
                   $path = $path.$strrchr;
                   $bool = move_uploaded_file($path,$dir_name.$path);
                   halt($bool);
               }
           }
       }
    }
}