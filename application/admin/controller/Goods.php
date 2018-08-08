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

    protected $goods_status = [
        0=>'1',
        1=>'0'
    ];
    /**
     * [商品列表]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     * 陈绪
     */
    public function index(){
        $goods = db("goods")->where("goods_status","<>","0")->paginate(10);
        $good_type_name = db("goods_type")->select();
        return view("goods_index",["goods"=>$goods,"goods_type_name"=>$good_type_name]);
    }

    public function add(){
        return view("goods_add");

    }

    /**
     * [商品添加]
     * 陈绪
     * @param Request $request
     */
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
           $goods_data["goods_status"] = $this->goods_status[0];
           $bool = db("goods")->insert($goods_data);
           if($bool){
               //取出图片在存到数据库
               $goods_images = [];
               $goodsid = db("goods")->getLastInsID();
               $file = request()->file('goods_images');
               foreach ($file as $value){
                   $info = $value->move(ROOT_PATH . 'public' . DS . 'upload');
                   $goods_url = str_replace("\\","/",$info->getSaveName());
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


    /**
     * [商品修改]
     * 陈绪
     */
    public function edit(Request $r){
        if($r->isPost()){
            $goods = db("goods")->where("id",$r->only(['id'])["id"])->select();
            $goods_images = db("goods_images")->where("goods_id",$r->only(['id'])["id"])->select();
            $goods_type = db("goods_type")->where("id",$goods[0]["goods_type"])->field("name")->select();
            $data = array("goods"=>$goods,"goods_type"=>$goods_type,"goods_images"=>$goods_images);
            return ajax_success("获取成功",$data);
        }
        return view("goods_edit");
    }


    /**
     * [商品删除]
     * 陈绪
     */
    public function del(Request $request){
        if ($request->isPost()){
            $id = $request->only(["id"])["id"];
            $id = explode(",",$id);
            global $goods_images;
            foreach ($id as $value){
                $goods_images = db("goods")->where("id",$value)->join("tb_goods_images gi","gi.goods_id=".$value)->delete();
            }
            if ($goods_images){
                return ajax_success("删除成功");
            }else{
                return ajax_error("删除成功");
            }

        }
    }


    /**
     * [产品更新]
     * 陈绪
     * @param Request $request
     */
    public function updata(Request $request){
        if($request->isPost()){
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
            $bool = db("goods")->update($goods_data);
            if($bool){
                //取出图片在存到数据库
                $goods_images = [];
                $goodsid = db("goods")->getLastInsID();
                $file = request()->file('goods_images');
                foreach ($file as $value){
                    $info = $value->move(ROOT_PATH . 'public' . DS . 'upload');
                    $goods_url = str_replace("\\","/",$info->getSaveName());
                    $goods_images[] = ["goods_images"=>$goods_url,"goods_id"=>$goodsid];
                }
                $booldata = model("goods_images")->isUpdate($goods_images);
                if($booldata){
                    $this->success("添加成功",url('admin/Goods/index'));
                }else{
                    $this->error("添加失败",url('admin/Goods/add'));
                }
            }
        }
    }

}