<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/13
 * Time: 14:46
 */

namespace app\admin\controller;
use think\Controller;
use think\Request;

class Discount extends Controller{

    public $status = [
        1=>"未使用",
        2=>"已使用",
        3=>"已失效"
    ];



    /**
     * 优惠券
     * 陈绪
     */
    public function index(){

        return view("discount_index");

    }



    /**
     * 添加优惠券
     * 陈绪
     */
    public function add(){
        return view("discount_add");
    }



    /**
     * 优惠券入库
     * 陈绪
     */
    public function save(Request $request){
        $data = $request->param();
        $time = $request->only(['over_time'])['over_time'];
        $discounts_valid_images = $request->file('discounts_valid_images')->move(ROOT_PATH . 'public' . DS . 'uploads');
        $data["discounts_valid_images"] = str_replace("\\","/",$discounts_valid_images->getSaveName());
        $discounts_failure_images = $request->file('discounts_failure_images')->move(ROOT_PATH . 'public' . DS . 'uploads');
        $data["discounts_failure_images"] = str_replace("\\","/",$discounts_failure_images->getSaveName());
        $data['start_time'] = time();
        $data['over_time'] = strtotime("+1 $time");
        $bool = db("discounts")->insert($data);
        if($bool){
            $this->success("添加成功",url("admin/Discount/index"));
        }else{
            $this->success("添加失败",url("admin/Discount/index"));
        }

    }

}