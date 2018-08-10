<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;


/**
 * [前端路由]
 * 陈绪
 */
Route::group("",[
    /*首页*/
    "/$"=>"index/index/index",


    /*商品列表*/
    "cat_index"=>"index/Cat/index",
    "cat_detail"=>"index/Cat/detail",


    /*分类*/
    "class_index"=>"index/Classify/index",
    "class_show"=>"index/Classify/show",
    "class_particulars"=>"index/Classify/particulars",


    /*购物车*/
    "shopping_index"=>"index/Shopping/index",


    /*我的页面*/
    "member_index"=>"index/Member/index",


    /*确认订单*/
    "order_index"=>"index/Order/index",


    /*登录页面*/
    "login"=>"index/Login/login",
    "logout"=>"index/Login/logout",


    /*验证码*/
    "login_captcha"=>"index/Login/captchas",


    /*注册页面*/
    "register"=>"index/Register/index",


    /*短信注册*/
    "doreg_phone" =>"index/Register/doRegByPhone",


    /*手机验证码*/
    "send_mobile_code"=>"index/Register/sendMobileCode",


    /*邮箱注册*/
    "doreg_email" =>"index/Register/doRegByEmail",



    /*找回密码页面*/
    "findpwd"=>"index/Findpwd/findpwd",

    /*晒单*/
    "share_detail"=>"index/Share/share_detail",
    "share_index"=>"index/Share/share_index",


]);

/**
 * [后台路由]
 * 陈绪
 */
Route::group("admin",[
    /*首页*/
    "/$"=>"admin/index/index",


    /*登录页面*/
    "index"=>"admin/Login/index",
    "login"=>"admin/Login/login",
    "logout"=>"admin/Login/logout",


    /*验证码*/
    "login_captcha"=>"admin/Login/captchas",

    /*管理员列表*/
    "admin_index"=>"admin/admin/index",
    "admin_add"=>"admin/admin/add",
    "admin_save"=>"admin/admin/save",
    "admin_del"=>"admin/admin/del",
    "admin_edit"=>"admin/admin/edit",
    "admin_updata"=>"admin/admin/updata",

    /*菜单列表*/
    "menu_index"=>"admin/menu/index",
    "menu_add"=>"admin/menu/add",
    "menu_save"=>"admin/menu/save",
    "menu_del"=>"admin/menu/del",
    "menu_edit"=>"admin/menu/edit",
    "menu_updata"=>"admin/menu/updata",


    /*角色列表*/
    "role_index"=>"admin/role/index",
    "role_add"=>"admin/role/add",
    "role_save"=>"admin/role/save",
    "role_del"=>"admin/role/del",
    "role_edit"=>"admin/role/edit",
    "role_updata"=>"admin/role/updata",

    /*商品管理*/
    "goods_index"=>"admin/Goods/index",
    "goods_add"=>"admin/Goods/add",
    "goods_save"=>"admin/Goods/save",
    "goods_edit"=>"admin/Goods/edit",
    "goods_updata"=>"admin/Goods/updata",
    "goods_del"=>"admin/Goods/del",
    "images_del"=>"admin/Goods/images",
    "goods_status"=>"admin/Goods/status",


    /*商品分类*/
    "category_index"=>"admin/Category/index",
    "category_add"=>"admin/Category/add",
    "category_save"=>"admin/Category/save",
    "category_edit"=>"admin/Category/edit",
    "category_del"=>"admin/Category/del",
    "category_updata"=>"admin/Category/updata",
    "category_ajax"=>"admin/Category/ajax_add",

    /*会员管理*/
    "user_index"=>"admin/User/index", //会员概况
    "user_search"=>"admin/User/search", //会员搜索
    "user_add"=>"admin/User/add",     //会员增加
    "user_save"=>"admin/User/save",     //会员增加(逻辑处理)
    "user_edit"=>"admin/User/edit",     //会员编辑
    "user_edits"=>"admin/User/edits",     //会员编辑
    "user_update"=>"admin/User/update",     //会员编辑更新
    "user_del"=>"admin/User/del",     //会员删除
    "user_dels"=>"admin/User/dels",     //会员批量删除
    "user_status"=>"admin/User/status",     //会员软删除禁用
    "user_statu"=>"admin/User/statu",     //会员软删除启用
    "user_cityss"=>"admin/User/cityss",  //会员等级
    "user_show"=>"admin/User/show",     //会员查看
    "user_shows"=>"admin/User/shows",     //会员查看
    "getRegion"=>"admin/User/getRegion",     //地区
    "pass_edit"=>"admin/User/pass_edit",     //会员密码编辑

    "user_grade"=>"admin/User/grade",  //会员等级
    "user_rule"=>"admin/User/rule",  //会员等级


    /*测试页面*/
    "text"=>"admin/Text/index",
]);


