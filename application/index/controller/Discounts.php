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
        $user_id = db("discounts_user")->field("user_id,count('user_id') tot")->having("tot =2")->group("user_id")->select();
        if(!empty($user_id)) {
            if ($user_id[0]['tot'] == 2) {
                $discounts_id = db("discounts")->field("id")->find();
                db("discounts_user")->where("user_id", $user_id[0]['user_id'])->update(['discounts_id' => $discounts_id['id']]);
                $discounts_data = db("discounts")->where("id",$discounts_id['id'])->select();
                $this->assign("discounts_data",$discounts_data);
            }
        }
        $time = time();
        db("discounts")->where("over_time","<",$time)->update(['status'=>3]);
        $discounts_status = db("discounts")->where("status","3")->select();
        //差优惠券以使用的状态
        return view("discounts_my",["discounts_status"=>$discounts_status]);
    }

}