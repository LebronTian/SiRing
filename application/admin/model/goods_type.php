<?php
namespace app\admin\model;

use think\Model;

class goods_type extends Model{

    public function sSave($arr){
        if(is_array($arr)){
            $this->save($arr);
        }
    }

}