<?php
/**
 * Created by PhpStorm.
 * User: CHEN
 * Date: 2018/7/11
 * Time: 16:12
 */

namespace app\admin\controller;

use think\console\Input;
use think\Controller;
use think\Request;
use think\Image;

class Goods extends Controller{
    /**
     * [商品列表]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     * 陈绪
     */
    public function index(Request $request){
        if($request->isPost()){
            $goods = db("goods")->select();
            $goods_type = db("goods_type")->select();
            $goods_images = db("goods_images")->select();
            return ajax_success("获取成功",array("goods"=>$goods[0],"goods_type"=>$goods_type[0],"goods_images"=>$goods_images[0]));
        }
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
                   $goods_images = [];
                   $goodsid = db("goods")->getLastInsID();
                   $file = request()->file('goods_images');
                   foreach ($file as $value){
                       $info = $value->move("" . 'public' . DS . 'uploads');
                       $goods_url = $info->getSaveName();
                       $goods_images[] = ["goods_images"=>$goods_url,"goods_id"=>$goodsid];
                   }
                   $booldata = model("goods_images")->saveAll($goods_images);
                   if($booldata){
                       $this->success("添加成功",url('admin/Goods/index'));
                   }else{
                       $this->error("添加失败",url('admin/Goods/add'));
                   }
           }
       }
    }
}