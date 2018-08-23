<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/23
 * Time: 15:50
 */
namespace  app\index\controller;
use think\Db;
use think\Request;
use think\Session;

class  Collection extends Base{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 我的收藏
     **************************************
     */
    public function index(){
        $member = Session::get('member');
        $member_data = Db::name('user')->where('phone_num',$member['phone_num'])->find();
        $member_id = $member_data['id'];
        if(!empty($member_id)){
            $data=Db::table("tb_collection")
                ->field("tb_collection.*,tb_goods.goods_bottom_money goods_bottom_money,tb_goods.goods_name goods_name,tb_goods.goods_show_images goods_show_images")
                ->join("tb_goods","tb_collection.goods_id=tb_goods.id and tb_collection.user_id=$member_id",'left')
                ->select();
            if(!empty($data)){
                $this->assign('data',$data);
            }
        }
        return view('index');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 添加收藏
     **************************************
     */
    public function  add(Request $request){
        if($request->isPost()){
            $member = Session::get('member');
            if(!empty($member)){
                $datas =$_POST;
                if(!empty($datas)){
                    $member_data = Db::name('user')->field('id')->where('phone_num',$member['phone_num'])->find();
                    $member_id = $member_data['id'];
                    $goods_id = $datas['id'];
                    $history_res =Db::name('collection')->where('user_id',$member_id)->where('goods_id',$goods_id)->find();
                    if($history_res){
                        $res = Db::name('collection')->where('user_id',$member_id)->where('goods_id',$goods_id)->delete();
                        if($res)
                        {
                            return ajax_success('取消收藏成功');
                        }
                    }
                    /*当同一个商品被同一个人再次收藏的时候就变为取消收藏*/
                    if(!$history_res){
                        $data =[
                            'user_id'=>$member_id,
                            'goods_id'=>$goods_id,
                        ];
                        $res = Db::name('collection')->insert($data);
                        if($res){
                            return ajax_success('收藏成功',$res);
                        }
                    }
                }

            }
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * 删除收藏
     **************************************
     */
    public function del(Request $request){
        if($request->isPost()){
            $id =$_POST['id'];
            if(is_array($id)){
                $where ='id in('.implode(',',$id).')';
            }else{
                $where ='id='.$id;
            }
            $list =  Db::name('collection')->where($where)->delete();
            if($list!==false)
            {
                $this->success('删除成功!');
            }else{
                $this->error('删除失败');
            }
        }
    }
}