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
        $data = $request->only(["status","goods_id","seckill_money"]);
        $start_time = $request->only(["start_time"])["start_time"];
        $over_time = $request->only(["over_time"])["over_time"];
        $data["start_time"] = strtotime($start_time);
        $data["over_time"] = strtotime($over_time);
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
        $data = $request->only(["status","goods_id","seckill_money"]);
        $start_time = $request->only(["start_time"])["start_time"];
        $over_time = $request->only(["over_time"])["over_time"];
        $data["start_time"] = strtotime($start_time);
        $data["over_time"] = strtotime($over_time);
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


}