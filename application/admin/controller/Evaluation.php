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
use think\Request;

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

    /**
     **************李火生*******************
     * @param Request $request
     * 评价图片详情
     **************************************
     */
    public function evalution_imgs(Request $request){
        if($request->isPost()){
            $data = $_POST;
            if(!empty($data)){
                $data_id =$data['id'];
                if(!empty($data_id)){
                    $order_id =Db::name('evaluate')->field('order_id')->where('id',$data_id)->find();
                 if(!empty($order_id)){
                    $evaluate_imgs =Db::name('evaluate_images')->field('images')->where('evaluate_order_id',$order_id['order_id'])->select();
                    if(!empty($evaluate_imgs)){
                        return ajax_success('成功',$evaluate_imgs);
                    }
                 }
                }
            }
        }
    }




}