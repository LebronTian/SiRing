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
            ->order('tb_evaluate.create_time','desc')
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
                    }else{
                        return ajax_success('失败');
                    }
                 }
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 评价审核
     **************************************
     */
    public function evalution_examine(Request $request){
        if($request->isPost()){
            $evalution_id =$request->only(['id'])['id'];
            $evalution_status =$request->only(['status'])['status'];
            /*审核通过*/
            if(!empty($evalution_id)&&$evalution_status ==1){
                $res = Db::name('evaluate')->where('id',$evalution_id)->update(['status'=>1]);
                if($res){
                    return ajax_success('审核通过成功',$res);
                }else{
                    return ajax_error('审核失败');
                }
            }
            /*审核不通过*/
            if(!empty($evalution_id)&&$evalution_status ==-1){
                $res = Db::name('evaluate')->where('id',$evalution_id)->update(['status'=>-1]);
                if($res){
                    return ajax_success('审核不通过成功',$res);
                }else{
                    return ajax_error('审核失败');
                }
            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 批量审核通过
     **************************************
     */
    public function  evalution_all_check(Request $request){
        if($request->isPost()){
            $id =$_POST['id'];
            if(is_array($id)){
                $where ='id in('.implode(',',$id).')';
            }else{
                $where ='id='.$id;
            }
            $list =  Db::name('evaluate')->where($where)->update(['status'=>1]);
            if($list!==false)
            {
                return ajax_success('审核成功');
            }else{
               return ajax_error('审核失败');
            }
        }
    }


}