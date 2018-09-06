<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/10
 * Time: 15:43
 */
namespace app\index\controller;
use app\index\model\Shoppings;
use think\Request;
use think\Session;
use think\Db;

class Shopping extends Base {

    /**
     * 购物车
     * 陈绪
     */
    public function index(){
        $data = Session::get("member");
        $user_id =Db::name('user')->field('id')->where('phone_num',$data['phone_num'])->find();
        $shopping = db("shopping")->where("user_id",$user_id['id'])->select();
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
                return ajax_error("请登录");
            }
            if(!empty($user_id['id'])) {
                //存入购物车
                $goods_id = $request->only(['id'])['id'];
                $goods_id = intval($goods_id);
                $goods = db("goods")->where("id",$goods_id)->find();
                $shopping = db("shopping")->where("user_id",$user_id['id'])->where("goods_id", $goods_id)->select();
                foreach ($shopping as $key=>$value) {
                    if (in_array($goods_id,$value)) {
                        $money = array($value['money'], $goods['goods_bottom_money']);
                        $shopping[$key]['money'] = array_sum($money);
                        $shopping[$key]['goods_unit'] = $value['goods_unit'] + 1;
                        unset($shopping[$key]['id']);
                        $bool = db("shopping")->where("goods_id", $goods_id)->where("user_id",$user_id['id'])->update($shopping[0]);
                        return ajax_success("成功", $bool);
                    }
                }
                $data['goods_name'] = $goods['goods_name'];
                $data['goods_images'] = $goods['goods_show_images'];
                $data['money'] = $goods['goods_bottom_money'];
                $data['goods_unit'] = 1;
                $data['user_id'] = $user_id['id'];
                $data['goods_id'] = $goods['id'];
                $bool = db("shopping")->insert($data);
                return ajax_success("获取成功", $bool);
            }
        }
    }



    /**
     * [购物车存储]
     * 陈绪
     */
    public function option(Request $request){
        if($request->isPost()){
            $data = Session::get("member");
            $user_id =Db::name('user')->field('id')->where('phone_num',$data['phone_num'])->find();
            unset($data);
            if(empty($user_id['id'])){
                $this->redirect('index/Login/login');
            }
            if(!empty($user_id['id'])){
                $id = $request->only(['id'])['id'];
                $goods_unit = $request->only(['goods_unit'])['goods_unit'];
                $money = $request->only(['money'])['money'];
                foreach ($id as $key => $val) {
                    db("shopping")->where("id", $val)->update(['goods_unit' => $goods_unit[$key]]);
                }
                //存储到购物车订单表中
                $data['money'] = $money;
                $data['shopping_id'] = implode(",", $id);
                $data['user_id'] = $user_id['id'];
                db("shopping_shop")->insert($data);
                $shopping_id['id'] = db("shopping_shop")->getLastInsID();
                Session("shopping", $shopping_id);
                return ajax_success("获取成功", $shopping_id);
            }
        }
    }



    public function batch(Request $r){
        if($r->isPost()){
            $id = $r->only(['del_id']);
            foreach ($id as $value){
                $bool = Shoppings::destroy($value);
            }
            if($bool){
                return ajax_success("删除成功",$bool);
            }else{
                return ajax_error("删除失败");
            }
        }
    }




}