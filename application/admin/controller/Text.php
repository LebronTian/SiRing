<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/4
 * Time: 14:29
 */
namespace app\admin\controller;
use think\Controller;

class Text extends Controller{
    public function index(){
        return view("index");
    }
}