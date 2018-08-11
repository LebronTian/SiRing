<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/11
 * Time: 11:57
 */
namespace  app\admin\controller;


use think\Controller;

class  Refund extends  Controller{
    /**
     **************李火生*******************
     * @return \think\response\View
     * 退款维权
     **************************************
     */
    public function  rights(){
        return view('refund_rights');
    }


}