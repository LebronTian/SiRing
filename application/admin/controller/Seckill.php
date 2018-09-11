<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/13
 * Time: 14:49
 */

namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Seckills;

class Seckill extends Controller{

    /**
     * 秒杀
     * 陈绪
     */
    public function index(){

        $seckill = db("seckill")->paginate(10);
        $goods = db("goods")->select();
        $goods_type = db("goods_type")->select();
        return view("seckill_index",["seckill"=>$seckill,"goods"=>$goods,"goods_type"=>$goods_type]);
    }


    /**
     * 秒杀产品添加
     * 陈绪
     */
    public function add(){
        return view("seckill_add");
    }


    /**
     * 秒杀入库
     * 陈绪
     */
    public function save(Request $request){
        $goods_id = $request->only(['goods_id'])['goods_id'];
        $data = $request->only(["status","seckill_money","keyword"]);
        $start_time = $request->only(["start_time"])["start_time"];
        $over_time = $request->only(["over_time"])["over_time"];
        $data['goods_id'] = $goods_id;
        $data["start_time"] = strtotime($start_time);
        $data["over_time"] = strtotime($over_time);
        $data['goods_num'] = $request->only(['goods_num'])['goods_num'];
        $show_images = $request->file("images")->move(ROOT_PATH . 'public' . DS . 'uploads');
        $data["images"] = str_replace("\\", "/", $show_images->getSaveName());
        $bool = db("seckill")->insert($data);
        if ($bool){
            $this->success("入库成功",url("admin/Seckill/index"));
        }else{
            $this->success("入库失败",url("admin/Seckill/add"));
        }
    }


    /**
     * 秒杀修改
     * 陈绪
     */
    public function edit(Request $request){
        $id = $request->only(["id"])['id'];
        $data = db("seckill")->where("id",$id)->select();
        return view("seckill_edit",["seckill"=>$data]);
    }



    /**
     * 秒杀更新入库
     * 陈绪
     */
    public function updata(Request $request){
        $id = $request->only(["id"])['id'];
        $data = $request->only(["status","goods_id","seckill_money","keyword","goods_num","residue_num"]);
        $start_time = $request->only(["start_time"])["start_time"];
        $over_time = $request->only(["over_time"])["over_time"];
        $data["start_time"] = strtotime($start_time);
        $data["over_time"] = strtotime($over_time);
        $show_images = $request->file("images")->move(ROOT_PATH . 'public' . DS . 'uploads');
        $data["images"] = str_replace("\\", "/", $show_images->getSaveName());
        $bool = db("seckill")->where("id",$id)->update($data);
        if ($bool){
            $this->success("更新成功",url("admin/Seckill/index"));
        }else{
            $this->success("更新失败",url("admin/Seckill/save"));
        }
    }



    /**
     * 秒杀删除
     * 陈绪
     */
    public function del(Request $request){
        $id = $request->only(["id"])["id"];
        $bool = db("seckill")->where("id",$id)->delete();
        if ($bool){
            $this->success("删除成功",url("admin/Seckill/index"));
        }else{
            $this->success("删除失败",url("admin/Seckill/index"));
        }
    }



    /**
     * [秒杀商品批量删除]
     * 陈绪
     */
    public function batches(Request $request){
        if($request->isPost()) {
            $id = $request->only(["ids"])["ids"];
            foreach ($id as $key=>$value) {
                $seckill_images = db("seckill")->where("id", $value)->field("images")->find();
                $bool = Seckills::destroy($value);
                unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $seckill_images["images"]);
            }
            if ($bool) {
                return ajax_success("删除成功");
            } else {
                return ajax_error("删除失败");
            }

        }
    }



}