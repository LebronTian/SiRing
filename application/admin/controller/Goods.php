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
use think\Db;
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
    public function index(Request $request){
        $datemins = $request->param("datemin");
        $datemaxs = $request->param("datemax");
        $search_keys = $request->param("search_key");
        $search_bts = $request->param("search_bt");
        $datemin = isset($datemins) ? $datemins : false;
        $datemax = isset($datemaxs) ? $datemaxs : false;
        $search_key = isset($search_keys) ? $search_keys : '%';
        $search_bt = isset($search_bts) ? $search_bts : false;
        if($request->isPost()) {
            if ($datemin && $datemax) {
               $good = db("goods")->where('create_time','>',strtotime($datemin))->where('create_time','<',strtotime($datemax))->paginate(5);
            }

            if ($search_key) {
                $good = db("goods")->where("goods_name","like","%".$search_key."%")->paginate(5);

            } else {
                $good = db("goods")->paginate(5);
            }

            return view("goods_index", [
                'good' => $good,
                'search_key' => $search_key,
                'datemax' => $datemax,
                'datemin' => $datemin
            ]);
        }else{
            $goods = db("goods")->paginate(10);
            return view("goods_index",["goods"=>$goods]);
        }

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
                        "goods_bottom_money",
                        "goods_keyword",
                        "goods_abstract",
                        "goods_detail",
           ]);
           $show_images = $request->file("goods_show_images")->move(ROOT_PATH . 'public' . DS . 'uploads');
           $goods_data["goods_show_images"] = str_replace("\\","/",$show_images->getSaveName());
           $goods_data["goods_status"] = $this->goods_status[0];
           $goods_data["create_time"] = time();
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
    public function edit(Request $r,$id){
        $goods = db("goods")->where("id",$id)->select();
        $goods_type = db("goods_type")->where("id",$goods[0]["goods_type_id"])->field("name,id")->select();
        $goods_images = db("goods_images")->where("goods_id",$id)->select();
        return view("goods_edit",["goods"=>$goods,"goods_type"=>$goods_type,"goods_images"=>$goods_images]);
    }


    /**
     * [图片删除]
     * 陈绪
     */
    public function images(Request $request){
        if($request->isPost()){
            $id = $request->only(['id'])['id'];
            $image_url = db("goods_images")->where("id",$id)->field("goods_images")->select();
            unlink(ROOT_PATH . 'public' . DS . 'upload/'.$image_url[0]['goods_images']);
            $bool = db("goods_images")->where("id",$id)->delete();
            if($bool){
                return ajax_success("删除成功");
            }else{
                return ajax_error("删除失败");
            }
        }

    }

    /**
     * [商品删除]
     * 陈绪
     */
    public function del(Request $request){
        if ($request->isPost()) {
            $id = $request->only(["id"])["id"];
            $image_url = db("goods_images")->where("goods_id", $id)->field("goods_images, id")->select();
            $goods_images = db("goods")->where("id", $id)->select();
            unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$goods_images[0]['goods_show_images']);
            foreach ($image_url as $value) {
                unlink(ROOT_PATH . 'public' . DS . 'upload/' . $value['goods_images']);
                db("goods_images")->where("id", $value['id'])->delete();
            }

            $bool = db("goods")->where("id", $id)->delete();
            if ($bool) {
                return ajax_error("删除成功");
            } else {
                return ajax_error("删除失败");
            }

        }
    }


    /**
     * [产品更新]
     * 陈绪
     * @param Request $request
     */
    public function updata(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->only(["id"])["id"];
            $goods_data = $request->only([
                "goods_name",
                "sort_number",
                "goods_type_id",
                "goods_specification",
                "goods_place",
                "goods_supplier",
                "goods_unit",
                "goods_bottom_money",
                "goods_keyword",
                "goods_abstract",
                "goods_detail",
            ]);
            $show_images = $request->file("goods_show_images")->move(ROOT_PATH . 'public' . DS . 'uploads');
            $goods_data["goods_show_images"] = str_replace("\\", "/", $show_images->getSaveName());
            $goods_data["goods_status"] = $this->goods_status[0];
            $goods_data["create_time"] = time();
            $bool = db("goods")->where("id", $id)->update($goods_data);
            if($bool){
                //取出图片在存到数据库
                $goods_images = [];
                $goodsid = db("goods")->getLastInsID();
                $file = request()->file('goods_images');
                foreach ($file as $value){
                    $info = $value->move(ROOT_PATH . 'public' . DS . 'upload');
                    $goods_url = str_replace("\\","/",$info->getSaveName());
                    $goods_images[] = ["goods_images"=>$goods_url,"goods_id"=>$id];
                }
                $booldata = model("goods_images")->saveAll($goods_images);
                if($booldata){
                    $this->success("添加成功",url('admin/Goods/index'));
                }else{
                    $this->error("添加失败",url('admin/Goods/edit'));
                }
            }
        }

    }


    /**
     * [商品状态]
     * 陈绪
     */
    public function status(Request $request){

        if ($request->isPost()){
            $goods_id = $request->only(['id'])['id'];
            $goods_status["goods_status"] = $this->goods_status[1];
            $bool = db("goods")->where("id",$goods_id)->update($goods_status);
            if ($bool){
                return 1;
            }else{
                return 0;
            }
        }

    }

    /**
     * [商品批量删除]
     * 陈绪
     */
    public function batches(Request $request){
        if($request->isPost()){
            $id = $request->only(["ids"])["ids"];
            halt($id);
        }
    }



}