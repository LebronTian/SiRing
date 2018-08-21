<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
class Admin extends Controller
{
    public $account_status = ["禁用","启用"];
    /**
     * [管理员列表]
     * 陈绪
     */
    public function index(){
        $account_list = db("admin")->order("id")->select();
        foreach ($account_list as $key=>$value){
            $account_list[$key]["role_name"] = db("role")->where("id",$value["role_id"])->value("name");
            $account_list[$key]["status"] = isset($this->account_status[$value["status"]]) ? $this->account_status[$value["status"]] : '未知';
        }
        //halt($account_list);
        $roleList = getSelectList("role");
        return view("index",["roleList"=>$roleList,"account_list"=>$account_list]);
    }

    /**
     * [管理员查询]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function add(){
        $roles = db("role")->where("status","1")->field("id,name")->select();
        $roleList = getSelectList("role");
        return view("save",["role"=>$roles,"roleList"=>$roleList]);
    }

    /**
     * [管理员添加入库]
     * 陈绪
     */
    public function save(Request $request){
        $data = $request->param();
        $data["passwd"] = md5($data["passwd"]);
        $data["stime"] = date("Y-m-d H:i:s");
        $boolData = model("Admin")->sSave($data);

        if($boolData){
            $this->success("添加成功",url("admin/admin/index"));
        }else{
            $this->error("添加失败",url("admin/admin/add"));
        }
    }

    /**
     * [管理员删除]
     * 陈绪
     */
    public function del($id){
        $bool = model("Admin")->where("id",$id)->delete();
        if($bool){
            $this->success("删除成功",url("admin/admin/index"));
        }else{
            $this->error("删除失败",url("admin/admin/index"));
        }
    }

    /**
     * [管理员编辑]
     * 陈绪
     */
    public function edit($id){
        $admin = db("Admin")->where("id","$id")->select();
        $roleList = getSelectList("role");
        return view("edit",["admin"=>$admin,"roleList"=>$roleList]);
    }

    /**
     * [管理员修改]
     * 陈绪
     */
    public function updata(Request $request){
        $data = $request->param();
        $data["passwd"] = md5($data["passwd"]);
        $data["stime"] = date("Y-m-d H:i:s");
        $id = $request->only(['id'])['id'];
        $bool = db("Admin")->where('id', $id)->update($data);
        if ($bool){
            $this->success("编辑成功",url("admin/admin/index"));
        }else{
            $this->error("编辑失败",url("admin/admin/edit"));
        }
    }



}