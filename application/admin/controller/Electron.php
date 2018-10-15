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
        $order = db("order")->where("status",">=",2)->where("status","<",11)->select();
        foreach ($order as $value){
            $goods = db("goods")->where("id",$value["goods_id"])->select();
            foreach ($goods as $val){
                if($val["id"] == $value["goods_id"]){
                    $goods_num[] = ["order_id"=>$value["id"],
                                    "goods_num"=>db("goods")->where("id",$value["goods_id"])->field("goods_num")->find()
                                    ,"time"=>date('Y-m-d H:i:s',strtotime("+1year",$value["create_time"]))];

                }
            }
        }
        return view("electron_index",["goods_num"=>$goods_num]);

    }



    /**
     * 电子保修卡添加
     * 陈绪
     */
    public function edit(Request $request){
        $data = $request->param();
        $order = db("order")->where("status",">=",2)->where("status","<",11)->select();
        foreach ($order as $value){
            $goods[] = db("goods")->where("id",$value['goods_id'])->select();
        }
        $bool = db("electron")->insert($data);
        if($bool){
            $this->success("添加成功");
        }
    }



    public function del($id){
        $bool = db("electron")->where("id",$id)->delete();
        if($bool){
            $this->success("删除成功",url("admin/Electron/index"));
        }
    }

}