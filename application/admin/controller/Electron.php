<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/29
 * Time: 11:46
 */
namespace app\admin\controller;
use think\Controller;
use think\Paginator;
use think\Request;

class Electron extends Controller{

    /**
     * 电子保修卡
     * 陈绪
     */
    public function index(Request $request){

        $electron = db("electron")->paginate(10);
        return view("electron_index",["electron"=>$electron]);

    }




    /**
     * 电子保修卡添加
     */
    public function add(){

        return view("electron_add");

    }



    /**
     * 电子保修卡修改显示
     * 陈绪
     */
    public function edit($id){

        $electron = db("electron")->where("id",$id)->select();
        return view("electron_edit",["electron"=>$electron]);
    }


    /**
     * [电子保修卡添加]
     * 陈绪
     * @param $id
     */
    public function save(Request $request){
        $electron = $request->param();
        $order = db("order")->where("id",$electron["order_id"])->find();
        if(is_numeric(trim($electron["year"]))){
            $year = $request->only(["year"])["year"];
            $electron["year"] = date("Y-m-d",strtotime("+$year day",$order["create_time"]));
        }else{
            $electron["year"] = date("Y-m-d",strtotime("+367 day",$order["create_time"]));
        }
        $bool = db("electron")->insert($electron);
        if($bool){
            $goods_bool = db("goods")->where("id",$order["goods_id"])->update(["goods_imei"=>$electron["goods_imei"]]);
            if($goods_bool){
                $this->success("添加成功",url("admin/Electron/index"));
            }
        }else{
            $this->error("添加失败",url("admin/Electron/index"));
        }

    }


    /**
     * 保修卡删除
     * 陈绪
     * @param $id
     */
    public function del($id){
        $bool = db("electron")->where("id",$id)->delete();
        if($bool){
            $this->success("删除成功",url("admin/Electron/index"));
        }
    }



    /**
     * 保修卡更新
     * 陈绪
     * @param Request $request
     */
    public function updata(Request $request){
        $id = $request->only(['id'])['id'];
        $electron = $request->param();
        $order = db("order")->where("id",$electron["order_id"])->find();
        if(is_numeric(trim($electron["year"]))){
            $year = $request->only(["year"])["year"];
            $electron["year"] = date("Y-m-d",strtotime("+$year day",$order["create_time"]));
        }else{
            $electron["year"] = date("Y-m-d",strtotime("+367 day",$order["create_time"]));
        }
        $bool = db("electron")->where("id",$id)->update($electron);
        if($bool){
            $this->success("编辑成功",url("admin/Electron/index"));
        }else{
            $this->error("编辑失败",url("admin/Electron/index"));
        }

    }





    public function search(Request $request){
        $goods_imei = $request->param("goods_imei");
        $search_bt = $request->param("search_bt");
        $goods_imeis = isset($goods_imei) ? $goods_imei : "%";
        $search_bts = isset($search_bt) ? $search_bt : false;
        if($goods_imeis){
            $data = db("electron")->where("goods_imei","like","%".$goods_imeis."%")->paginate(5, false,['query' => request()->param()]);;

        }else {
            $data = db("electron")->paginate(5,false,['query' => request()->param()]);
        }
        return view("electron_index",["data"=>$data]);
    }

}