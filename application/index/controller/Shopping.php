<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/10
 * Time: 15:43
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Cache;

class Shopping extends Controller{

    /**
     * 购物车
     * 陈绪
     */
    public function index(){
        $shopping = db("shopping")->select();
        return view("shopping_index",["shopping"=>$shopping]);
    }


    /**
     *获取商品id
     * 陈绪
     */
    public function ajax_id(Request $request){
        if ($request->isPost()){
            $data = Session::get("member");
            $user_id =Db::name('user')->field('id')->where('phone_num',$data['phone_num'])->find();
            unset($data);
            if(empty($user_id['id'])){
                $this->redirect('index/Login/login');
            }
            if(!empty($user_id['id'])) {
                //存入购物车
                $goods_id = $request->only(['id'])['id'];
                $goods = db("goods")->where("id",$goods_id)->find();
                $shopping = db("shopping")->select();
                foreach ($shopping as $key=>$value){
                    if($goods_id == $value['goods_id']){
                        $money = array($value['money'],$goods['goods_bottom_money']);
                        $shopping[$key]['goods_num'] = $value['goods_num']+1;
                        $shopping[$key]['money'] = array_sum($money);
                        $shopping[$key]['goods_unit'] = $value['goods_unit']+1;
                        $bool = db("shopping")->where("goods_id",$goods_id)->update($shopping[0]);
                        return ajax_success("获取成功",$bool);
                    }

                }
                $data['goods_name'] = $goods['goods_name'];
                $data['goods_images'] = $goods['goods_show_images'];
                $data['money'] = $goods['goods_bottom_money'];
                $data['goods_unit'] = $goods['goods_unit'];
                $data['user_id'] = $user_id['id'];
                $data['goods_id'] = $goods['id'];
                $data['goods_num'] = 1;
                db("shopping")->insert($data);
                return ajax_success("获取成功", $data);
            }
        }
    }



    /**
     * [购物车存储]
     * 陈绪
     */
    public function option(){

    }




}