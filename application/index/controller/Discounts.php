<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/24
 * Time: 11:20
 */

namespace app\index\controller;
use think\Controller;
use think\Session;

class Discounts extends Base{


    /**
     * 优惠券
     * 陈绪
     */
    public function index(){
        $data = Session::get("member");
        $user_id =db('user')->field('id')->where('phone_num',$data['phone_num'])->find();
        $number = "866".$user_id['id'];
        return view("discounts_index",['number'=>$number,['user_id'=>$user_id]]);

    }



    /**
     * 我的优惠券
     * 陈绪
     */
    public function discounts_my(){
        //取出表中user_id数量为两条的字段名
        $user_id = db("discounts_user")->where("discounts_id",null)->field("user_id,count('user_id') tot")->having("tot =2")->group("user_id")->find();
        $user = Session::get("member");
        $user = db("user")->where("phone_num",$user['phone_num'])->find();
        if(!empty($user_id)) {
            if ($user_id['tot'] == 2) {
                $discounts_id = db("discounts")->where("status",1)->where("user_id",null)->field("id")->find();
                $bool = db("discounts_user")->where("user_id", $user['id'])->update(['discounts_id' => $discounts_id['id']]);
                if($bool == true){
                    db("discounts")->where("id",$discounts_id['id'])->update(["user_id"=>$user_id['user_id']]);
                }
                $discounts_data = db("discounts")->where("id",$discounts_id['id'])->where("user_id",$user_id["user_id"])->select();
                $this->assign("discounts_data",$discounts_data);
            }
        }
        $time = time();
        db("discounts")->where("over_time","<",$time)->update(['status'=>3]);
        $discounts_status = db("discounts")->where("status","3")->select();
        $discounts = db("discounts")->where("user_id",$user_id["user_id"])->select();
        return view("discounts_my",["discounts_status"=>$discounts_status,"discounts"=>$discounts]);
    }

}