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
use app\admin\model\Good;
use app\admin\model\GoodsImages;
use think\Session;

class Goods extends Controller{

    public $goods_status = [0,1];
    /**
     * [商品列表]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     * 陈绪
     */
    public function index(Request $request){

        $goods = db("goods")->order("id desc")->paginate(10);
        return view("goods_index",["goods"=>$goods]);

    }


    /**
     * 模糊查询
     * 陈绪
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function seach(Request $request){
        $datemins = $request->param("datemin");
        $datemaxs = $request->param("datemax");
        $search_keys = $request->param("search_key");
        $search_bts = $request->param("search_bt");
        $datemin = isset($datemins) ? $datemins : false;
        $datemax = isset($datemaxs) ? $datemaxs : false;
        $search_key = isset($search_keys) ? $search_keys : '%';
        $search_bt = isset($search_bts) ? $search_bts : false;

        if ($datemin && $datemax) {
            $good = db("goods")->where('create_time', '>', strtotime($datemin))->where('create_time', '<', strtotime($datemax))->paginate(5, false,['query' => request()->param()]);
            $this->assign("good",$good);
        }

        if ($search_key) {
            $good = db("goods")->where("goods_name", "like", "%" . $search_key . "%")->paginate(5, false,['query' => request()->param()]);
        } else {
            $good = db("goods")->paginate(5,false,['query' => request()->param()]);
            $this->assign("good", $good);
        }
        return view("goods_index", [
            'good' => $good,
            'search_key' => $search_key,
            'datemax' => $datemax,
            'datemin' => $datemin
        ]);

    }


    /**
     * 商品添加
     * 陈绪
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function add(){
        return view("goods_add");
    }

    /**
     * [商品添加]
     * 陈绪
     * @param Request $request
     */
    public function save(Request $request)
    {
        if ($request->isPost()) {
            $goods_data = $request->param();
            $sign = $request->only(["goods_sign"])["goods_sign"];
            $goods_data["goods_sign"] = implode(",", $sign);
            $goods_data["goods_number"] = $request->only(["goods_number"])["goods_number"];
            //图片添加
            $show_images = $request->file("goods_show_images");
            $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'uploads');
            $goods_data["goods_show_images"] = str_replace("\\", "/", $show_image->getSaveName());

            $goods_parts_big_img = $request->file("goods_parts_big_img")->move(ROOT_PATH . 'public' . DS . 'uploads');
            $goods_data["goods_parts_big_img"] = str_replace("\\", "/", $goods_parts_big_img->getSaveName());

            $goods_spec_img = $request->file("goods_spec_img")->move(ROOT_PATH . 'public' . DS . 'uploads');
            $goods_data["goods_spec_img"] = str_replace("\\", "/", $goods_spec_img->getSaveName());

            $goods_parts_img = $request->file("goods_parts_img")->move(ROOT_PATH . 'public' . DS . 'uploads');
            $goods_data["goods_parts_img"] = str_replace("\\", "/", $goods_parts_img->getSaveName());

            $goods_data["create_time"] = time();
            $bool = db("goods")->insert($goods_data);
            if ($bool) {
                //取出图片在存到数据库
                $goods_images = [];
                $goodsid = db("goods")->getLastInsID();
                $file = request()->file('goods_images');
                foreach ($file as $key => $value) {
                    $info = $value->move(ROOT_PATH . 'public' . DS . 'upload');
                    $goods_url = str_replace("\\", "/", $info->getSaveName());
                    $goods_images[] = ["goods_images" => $goods_url, "goods_id" => $goodsid];
                }

                $goods_quality_img = $request->file("goods_quality_img");
                foreach ($goods_quality_img as $val) {
                    $goods_quality_imgs = $val->move(ROOT_PATH . 'public' . DS . 'upload');
                    $goods_quality_imgs_url = str_replace("\\", "/", $goods_quality_imgs->getSaveName());
                    $goods_images[] = ["goods_quality_img" => $goods_quality_imgs_url, "goods_id" => $goodsid];
                }
                $booldata = model("goods_images")->saveAll($goods_images);
                if ($booldata) {
                    $this->redirect('admin/Goods/index');
                } else {
                    $this->redirect('admin/Goods/index');
                }
            }
        }
    }


    /**
     * [商品修改]
     * 陈绪
     */
    public function edit(Request $r){
        $id = Session::get("goodsid");
        if(isset($id)){
            $goods = db("goods")->where("id",$id)->select();
            $goods_type = db("goods_type")->where("id",$goods[0]["goods_type_id"])->field("name,id")->select();
            $goods_images = db("goods_images")->where("goods_id",$id)->select();
            Session::delete("goodsid");
            $this->assign([
                "goods"=>$goods,
                "goods_type"=>$goods_type,
                "goods_images"=>$goods_images
            ]);
        }else{
            $id = $r->only(['id'])['id'];
            $goods = db("goods")->where("id",$id)->select();
            $goods_type = db("goods_type")->where("id",$goods[0]["goods_type_id"])->field("name,id")->select();
            $goods_images = db("goods_images")->where("goods_id",$id)->select();
            $this->assign([
                "goods"=>$goods,
                "goods_type"=>$goods_type,
                "goods_images"=>$goods_images
            ]);
        }

        return view("goods_edit");
    }


    /**
     * [图片删除]
     * 陈绪
     */
    public function images(Request $request){
        if($request->isPost()){
            $id = $request->only(['id'])['id'];
            $image_url = db("goods_images")->where("id",$id)->field("goods_images,goods_quality_img")->find();
            if($image_url['goods_images'] != null){
                unlink(ROOT_PATH . 'public' . DS . 'upload/'.$image_url['goods_images']);
            }

            if($image_url['goods_quality_img'] != null){
                unlink(ROOT_PATH . 'public' . DS . 'upload/'.$image_url['goods_quality_img']);
            }
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
            $image_url = db("goods_images")->where("goods_id", $id)->field("goods_images,goods_quality_img,id")->select();
            $goods_images = db("goods")->where("id", $id)->select();
            unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_images[0]['goods_show_images']);
            unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_images[0]['goods_parts_big_img']);
            unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_images[0]['goods_spec_img']);
            unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_images[0]['goods_parts_img']);
            foreach ($image_url as $value) {
                if ($value['goods_images'] != null) {
                    unlink(ROOT_PATH . 'public' . DS . 'upload/' . $value['goods_images']);
                }
                if ($value['goods_quality_img'] != null) {
                    unlink(ROOT_PATH . 'public' . DS . 'upload/' . $value['goods_quality_img']);
                }
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
                "goods_new_money",
                "goods_parts",
                "goods_status",
                "goods_bottom_money",
                "goods_num",
                "goods_date"
            ]);
            $sign = $request->only(["goods_sign"])["goods_sign"];
            $goods_data["goods_sign"] = implode(",", $sign);
            $goods_data["goods_number"] = $request->only(["goods_number"])["goods_number"];
            //图片添加
            $show_images = $request->file("goods_show_images");
            if(!empty($show_images)){
                $show_image = $show_images->move(ROOT_PATH . 'public' . DS . 'uploads');
                $goods_data["goods_show_images"] = str_replace("\\", "/", $show_image->getSaveName());
            }
            $goods_parts_big_img = $request->file("goods_parts_big_img");
            if(!empty($goods_parts_big_img)){
                $goods_parts_big_imgs = $goods_parts_big_img->move(ROOT_PATH . 'public' . DS . 'uploads');
                $goods_data["goods_parts_big_img"] = str_replace("\\", "/", $goods_parts_big_imgs->getSaveName());

            }

            $goods_spec_img = $request->file("goods_spec_img");
            if(!empty($goods_spec_img)){
                $goods_spec_imgs = $goods_spec_img->move(ROOT_PATH . 'public' . DS . 'uploads');
                $goods_data["goods_spec_img"] = str_replace("\\", "/", $goods_spec_imgs->getSaveName());
            }

            $goods_parts_img = $request->file("goods_parts_img");
            if(!empty($goods_parts_img)){
                $goods_parts_imgs = $goods_parts_img->move(ROOT_PATH . 'public' . DS . 'uploads');
                $goods_data["goods_parts_img"] = str_replace("\\", "/", $goods_parts_imgs->getSaveName());
            }

            $goods_data["create_time"] = time();
            $bool = db("goods")->where("id", $id)->update($goods_data);
            if($bool){
                //取出图片在存到数据库
                $goods_images = [];
                $goodsid = db("goods")->getLastInsID();
                $file = request()->file('goods_images');
                if(!empty($file)){
                    foreach ($file as $key => $value) {
                        $info = $value->move(ROOT_PATH . 'public' . DS . 'upload');
                        $goods_url = str_replace("\\", "/", $info->getSaveName());
                        $goods_images[] = ["goods_images" => $goods_url, "goods_id" => $id];
                    }
                }

                $goods_quality_img = $request->file("goods_quality_img");
                if(!empty($goods_quality_img)){
                    foreach ($goods_quality_img as $val) {
                        $goods_quality_imgs = $val->move(ROOT_PATH . 'public' . DS . 'upload');
                        $goods_quality_imgs_url = str_replace("\\", "/", $goods_quality_imgs->getSaveName());
                        $goods_images[] = ["goods_quality_img" => $goods_quality_imgs_url, "goods_id" => $id];
                    }
                }
                $booldata = model("goods_images")->saveAll($goods_images);
                if ($booldata) {
                    $this->redirect('admin/Goods/index');
                } else {
                    $this->redirect('admin/Goods/index');
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
            $goods_status["goods_status"] = $this->goods_status[0];
            $bool = db("goods")->where("id",$goods_id)->update($goods_status);
            if ($bool){
                return ajax_success("更新成功");
            }else{
                return ajax_error("更新失败");
            }
        }

    }


    /**
     * [商品上架]
     * 陈绪
     * @param Request $request
     * @return
     */
    public function putaway(Request $request){
        if ($request->isPost()){
            $goods_id = $request->only(['id'])['id'];
            $goods_status["goods_status"] = $this->goods_status[1];
            $bool = db("goods")->where("id",$goods_id)->update($goods_status);
            if ($bool){
                return ajax_success("更新成功");
            }else{
                return ajax_error("更新失败");
            }
        }
    }



    /**
     * [商品批量删除]
     * 陈绪
     */
    public function batches(Request $request){
        if($request->isPost()) {
            $id = $request->only(["ids"])["ids"];
            foreach ($id as $value) {
                $goods_url = db("goods")->where("id", $value)->find();
                $goods_images = db("goods_images")->where("goods_id", $value)->select();
                if($goods_url['goods_show_images'] != null){
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_url['goods_show_images']);
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_url['goods_parts_big_img']);
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_url['goods_spec_img']);
                    unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $goods_url['goods_parts_img']);
                }
                foreach ($goods_images as $val) {
                    if ($val['goods_images'] != null) {
                        unlink(ROOT_PATH . 'public' . DS . 'upload/' . $val['goods_images']);
                    }
                    if ($val['goods_quality_img'] != null) {
                        unlink(ROOT_PATH . 'public' . DS . 'upload/' . $val['goods_quality_img']);
                    }
                    GoodsImages::destroy($val['id']);
                }
                $bool = Good::destroy($value);
            }
            if ($bool) {
                return ajax_success("删除成功");
            } else {
                return ajax_error("删除失败");
            }

        }
    }



    /**
     * 商品克隆
     * 陈绪
     */
    public function goods_clone(Request $request){

        if($request->isPost()){
            $id = $request->only(['id'])['id'];
            $goods = db("goods")->where("id",$id)->find();
            $dir_name = (ROOT_PATH . 'public' . DS . 'uploads/'."SB".date("Ymd"));
            if(!is_dir($dir_name)){
                mkdir(ROOT_PATH . 'public' . DS . 'uploads/'."SB".date("Ymd"),777);
            }
            $goods["goods_show_images"] = new_images($goods["goods_show_images"],$dir_name);
            $goods["goods_parts_big_img"] = new_images($goods["goods_parts_big_img"],$dir_name);
            $goods["goods_spec_img"] = new_images($goods["goods_spec_img"],$dir_name);
            $goods["goods_parts_img"] = new_images($goods["goods_parts_img"],$dir_name);
            $goods_images = db("goods_images")->where("goods_id",$id)->select();
            unset($goods["id"]);
            $bool = db("goods")->insert($goods);
            if($bool){
                $goodsid = db("goods")->getLastInsID();
                foreach ($goods_images as $key=>$val){
                    $goods_images[$key]["goods_id"] = $goodsid;
                    if($val['id']){
                        unset($val["id"]);
                    }
                    $dir_names = (ROOT_PATH . 'public' . DS . 'upload/'."SB".date("Ymd"));
                    if(!is_dir($dir_names)){
                        $name_bool = mkdir(ROOT_PATH . 'public' . DS . 'upload/'."SB".date("Ymd"),777);
                    }

                    if($val["goods_images"] != null){
                        $val["goods_images"] = new_image($val["goods_images"],$dir_names);
                    }
                    if($val["goods_quality_img"] != null){
                        $val["goods_quality_img"] = new_image($val["goods_quality_img"],$dir_names);
                    }
                    $bool_images = db("goods_images")->insert(["goods_id"=>$goodsid,"goods_images"=>$val["goods_images"],"goods_quality_img"=>$val["goods_quality_img"]]);
                }
                if($bool_images){
                    Session("goodsid",$goodsid);
                    return ajax_success("添加成功",$goodsid);
                }

            }
        }



    }



}