<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/11
 * Time: 11:56
 */
namespace  app\admin\controller;

use think\Controller;

class  Evaluation extends  Controller{

    /**
     **************李火生*******************
     * @return \think\response\View
     * 评价管理
     **************************************
     */
    public function management(){
        return view('evaluation_management');

    }
}