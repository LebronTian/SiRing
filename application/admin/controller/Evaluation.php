<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/11
 * Time: 11:56
 */
namespace  app\admin\controller;

use think\Controller;
use think\Db;

class  Evaluation extends  Controller{

    /**
     **************李火生*******************
     * @return \think\response\View
     * 评价管理
     **************************************
     */
    public function management(){

       $data=Db::table("tb_evaluate")
            ->field("tb_evaluate.*,tb_goods.goods_name goods_name,tb_goods.goods_show_images goods_show_images ,tb_user.user_name user_name")
            ->join("tb_goods","tb_evaluate.goods_id=tb_goods.id",'left')
            ->join("tb_user","tb_evaluate.user_id=tb_user.id",'left')
            ->select();
       if($data)
       {
           $this->assign('data',$data);
       }

       return view('evaluation_management');
    }




}