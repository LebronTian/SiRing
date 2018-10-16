<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/16
 * Time: 10:09
 */
namespace app\admin\controller;
use think\Controller;

class Recommend extends Controller{

    public function index(){
        return view("recommend_index");
    }



    public function add(){
        return view("recommend_add");
    }



    public function save(){

    }



    public function edit(){
        return view("recommend_edit");
    }



    public function updata(){

    }



    public function del(){

    }



}