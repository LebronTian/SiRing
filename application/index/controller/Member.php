<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/7
 * Time: 15:05
 */
namespace  app\index\controller;

use think\Controller;
use think\Request;
use  think\Db;
use think\Response;
use think\Session;

class  Member extends  Base {
    /**
     **************李火生*******************
     * @return \think\response\View]
     * 用户页面
     **************************************
     */
    public  function  index(){
        return view('member_index');
    }

    /**
     **************李火生*******************
     * @param Request $request
     * @return \think\response\View|void
     * 收货地址管理
     **************************************
     */

    public function address(Request $request){
        $province = Db::name('tree')->where (array('pid'=>1) )->select();
        $this->assign('province',$province);
        // if($request->isPost()){
        //     $data =Session('member');
        //     $member_id =Db::name('user')->field('id')->where('phone_num',$data['phone_num'])->find();
        //     $data =Db::name('user')->where('id',$member_id)->find();
        //     return ajax_success('获取成功',$data);
        // }
        return view('address');
    }

    public function getRegions(){
        $Region=Db::name("tree");
        $map['pid']=$_REQUEST["pid"];
        $map['type']=$_REQUEST["type"];
        $list=$Region->where($map)->select();
        echo json_encode($list);
    }

    public function  harvester_informations(Request $request){
        if($request->isPost()){
            $data =$_POST;
            if(!empty($data)){
                return ajax_success('成功',$data);
            }
        }
    }


}