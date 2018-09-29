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
    "goods_index"=>"index/Goods/index",
    "goods_detail"=>"index/Goods/detail",
    "goods_id"=>"index/Goods/ajax_id",
    "particulars_id"=>"index/Goods/goods_id",
    "goods_big_images"=>"index/Goods/big_images",


    /*分类*/
    "class_index"=>"index/Classify/index",
    "class_show"=>"index/Classify/show",
    "class_particulars"=>"index/Classify/particulars",


    /*购物车*/
    "shopping_index"=>"index/Shopping/index",
    "shopping_id"=>"index/Shopping/ajax_id",
    "shopping_option"=>"index/Shopping/option",
    "shopping_batch"=>"index/Shopping/batch",


    /*秒杀*/
    "seckill_index"=>"index/Seckill/index",


    /*我的收藏*/
    "collection"=>"index/Collection/index",
    /*添加收藏*/
    "collection_add"=>"index/Collection/add",
    /*删除收藏*/
    "collection_del"=>"index/Collection/del",
    /*收藏的样式改变需要返回一个状态值给页面*/
    "show_collection"=>"index/Collection/show_collection",



    /*我的页面*/
    "member_index"=>"index/Member/index",
    /*我的页面的用户名信息*/
    "get_user_information"=>"index/Member/get_user_information",
    /*个人资料添加*/
    "member_edit"=>"index/member/member_edit",
    "member_edit_active"=>"index/Member/member_edit_active",
    /*如果有个人信息则进行编辑*/
    "get_member_information"=>"index/Member/get_member_information",
    /*TODO:个人头像上传(未实现)*/
    "user_add_img"=>"index/Member/user_add_img",
    /*收货地址*/
    "address"=>"index/Member/address",
    // 我的地址
    "myadd"=>"index/Member/myadd",
    /*三级城市*/
    "getRegions"=>"index/Member/getRegions",
    /*收获人信息管理*/
    'harvester_informations'=>"index/Member/harvester_informations",
    /*收货人信息编辑查看*/
    'get_address_informations'=>"index/Member/get_address_informations",



    /*确认订单*/
    "order_index"=>"index/Order/index",
    "common_id"=>"index/Order/common_id",
    'bt_order'=>"index/Order/bt_order",//提交订单


    'order_details'=>"index/Order/details",//订单详情
    'order_id'=>"index/Order/ajax_id",//订单详情
    'read_order_to_pay'=>"index/Order/read_order_to_pay",//我的订单待支付支付返回值
    'save_order_information_number'=>'index/Order/save_order_information_number',//保存来自于取消支付宝付款
    'order_detail_del'=>'index/Order/order_detail_del', //订单详情页面的取消按钮
    'order_to_pay_by_number'=>'index/Order/order_to_pay_by_number', //订单详情页面的付款按钮


    'check_logistic'=>"index/Order/logistic",//查看物流
    'order_myorder'=>"index/Order/myorder",//我的订单
    'order_wait_pay'=>"index/Order/wait_pay",//待支付
    'order_wait_deliver'=>"index/Order/wait_deliver",//待发货
    'order_take_deliver'=>"index/Order/take_deliver",//待收获
    'order_evaluate'=>"index/Order/evaluate",//待评价
    'refund'=>"index/Order/refund",//退款/售后
    'cancel_order'=>"index/Order/cancel_order",//买家取消订单
    'collect_goodss'=>"index/Order/collect_goods",//买家确认收货
    'logistics_information'=>"index/Order/logistics_information",//实时物流信息
    'interface_information'=>"index/Order/interface_information", //快递100接口
    'logistics_information_id'=>"index/Order/logistics_information_id",//用来接收物流信息的id
    'confirm_payment'=>"index/order/confirm_payment", //确定付款


    'service_index'=>"index/self_service/index", //自助服务
    'repair_index'=>"index/self_service/repair", //售后维修
    'address_edit'=>"index/self_service/address_edit", //售后地址修改
    'repair_desc'=>"index/self_service/repair_desc", //问题描述
    'repair_desc_edit'=>"index/self_service/repair_desc_edit", //问题描述
    'successful_sub'=>"index/self_service/successful_sub", //提交成功
    'processing'=>"index/self_service/processing", //处理中
    'evaluations'=>"index/self_service/evaluation", //待评价
    'detail_info'=>"index/self_service/detail_info", //服务单详情
    'detail_ajax'=>"index/self_service/repair_ajax", //获取售后商品信息
    'detail_del'=>"index/self_service/repair_del", //售后服务订单取消
    'detail_images'=>"index/self_service/detail_images", //售后图片删除
    'detail_updata'=>"index/self_service/updata", //售后服务订单更新


    'electronices'=>'index/self_service/electronics',//电子保修
    'agreement'=>'index/self_service/agreement',//售后服务协议
   




    /*登录页面*/
    "login"=>"index/Login/login",
    /*登录操作*/
    'Dolog'=>"index/Login/Dolog",
    /*退出登录*/
    "logout"=>"index/Login/logout",

    /*验证码*/
    "login_captcha"=>"index/Login/captchas",


    /*注册页面*/
    "register"=>"index/Register/index",
    "register_code"=>"index/Register/code",


    /*短信注册*/
    "doreg_phone" =>"index/Register/doRegByPhone",
    /*手机验证码*/
    "send_mobile_code"=>"index/Register/sendMobileCode",
    /*邮箱注册*/
    "doreg_email" =>"index/Register/doRegByEmail",



    /*找回密码页面*/
    "findpwd"=>"index/Findpwd/findpwd",
    'find_password_by_phone'=>"index/findpwd/find_password_by_phone",//用于操作手机找回密码

    /*晒单*/
    "share_detail"=>"index/Share/share_detail",
    "share_index"=>"index/Share/share_index",
    "all_information"=>"index/Share/all_information",//点击全部的时候所有信息
    "all_information_share"=>"index/Share/all_information_share",//一进晒单页面就显示所有信息
    /*晒单详情页面获取信息*/
    'share_evaluation'=>"index/Share/share_evaluation",
    /*获取晒单的详细评价图片信息*/
    'get_evalution_imgs'=>"index/Share/get_evalution_imgs",
    /*晒单页面下拉商品类别*/
    'phone_type'=>"index/Share/phone_type",
    /*晒单手机类型返回的数据点击发送一个id过来让我作为判断显示的条件*/
    'get_phone_type_informations'=>"index/Share/get_phone_type_informations",

    /*通过点击评价传一个order_id过去确定是哪个订单的评价*/
    "evaluation_get_order_id"=>"index/Share/evaluation_get_order_id",
    /*评价页面*/
    "evaluation"=>"index/Share/evaluation",
    /*添加评价*/
    "evaluation_add"=>"index/Share/evaluation_add",
    /**
     * 图片
     */
    "evaluation_add_img"=>"index/Share/evaluation_add_img",


    /*优惠券*/
    "discounts_index"=>"index/Discounts/index",
    "discounts_my"=>"index/Discounts/discounts_my",

    /*在线客服*/
    "chat"=>"index/chat/chat",
    /*用户发送信息*/
    "chat_pull"=>"index/Chat/chat_push",
    /*接收客服发送回来的信息*/
    "chat_push"=>"index/Chat/chat_pull",



    /*支付*/
    "Alipay_index"=>"index/Alipay/aliPay",
    "Alipay_pay_code"=>"index/Alipay/pay_code",


    /*常见问题*/
    "issue_index"=>"index/Issue/index",
    'common_problem_details'=>'index/Issue/common_problem_details',//常见问题详情
    "issue_details"=>"index/Issue/details",

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
    "admin_status"=>"admin/admin/status",



    /*菜单列表*/
    "menu_index"=>"admin/menu/index",
    "menu_add"=>"admin/menu/add",
    "menu_save"=>"admin/menu/save",
    "menu_del"=>"admin/menu/del",
    "menu_edit"=>"admin/menu/edit",
    "menu_updata"=>"admin/menu/updata",
    "menu_status"=>"admin/menu/status",


    /*角色列表*/
    "role_index"=>"admin/role/index",
    "role_add"=>"admin/role/add",
    "role_save"=>"admin/role/save",
    "role_del"=>"admin/role/del",
    "role_edit"=>"admin/role/edit",
    "role_updata"=>"admin/role/updata",
    "role_status"=>"admin/role/status",


    /*商品管理*/
    "goods_index"=>"admin/Goods/index",
    "goods_add"=>"admin/Goods/add",
    "goods_save"=>"admin/Goods/save",
    "goods_edit"=>"admin/Goods/edit",
    "goods_updata"=>"admin/Goods/updata",
    "goods_del"=>"admin/Goods/del",
    "images_del"=>"admin/Goods/images",
    "images_dels"=>"admin/Goods/image",
    "goods_status"=>"admin/Goods/status",
    "goods_batches"=>"admin/Goods/batches",
    "goods_putaway"=>"admin/Goods/putaway",


    /*商品分类*/
    "category_index"=>"admin/Category/index",
    "category_add"=>"admin/Category/add",
    "category_save"=>"admin/Category/save",
    "category_edit"=>"admin/Category/edit",
    "category_del"=>"admin/Category/del",
    "category_updata"=>"admin/Category/updata",
    "category_ajax"=>"admin/Category/ajax_add",
    "category_images"=>"admin/Category/images",


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
    "user_show"=>"admin/User/show",     //会员查看
    "user_shows"=>"admin/User/shows",     //会员查看
    "getRegion"=>"admin/User/getRegion",     //三级地区
    "pass_edit"=>"admin/User/pass_edit",     //会员密码编辑



    /*优惠券*/
    "discount_index"=>"admin/Discount/index",
    "discount_add"=>"admin/Discount/add",
    "discount_save"=>"admin/Discount/save",
    "discount_edit"=>"admin/Discount/edit",
    "discount_updata"=>"admin/Discount/updata",
    "discount_del"=>"admin/Discount/del",
    "discount_batches"=>"admin/Discount/batches",


    /*秒杀*/
    "seckill_index"=>"admin/Seckill/index",
    "seckill_add"=>"admin/Seckill/add",
    "seckill_save"=>"admin/Seckill/save",
    "seckill_edit"=>"admin/Seckill/edit",
    "seckill_updata"=>"admin/Seckill/updata",
    "seckill_del"=>"admin/Seckill/del",
    "seckill_batches"=>"admin/Seckill/batches",

    /*拼团*/
    "group_index"=>"admin/Group/index",


    /*小程序二维码*/
    "procedure_index"=>"admin/Procedure/index",





    /*订单管理*/
    "order_index"=>"admin/Order/index",
    "order_search"=>"admin/Order/search",//模糊查询
    "batch_delivery"=>"admin/Order/batch_delivery",//批量发货
    "pending_payment"=>"admin/Order/pending_payment",//代发货
    'order_refuse'=>"admin/Order/refuse", //商家取消买家订单
    "express_number"=>"admin/Order/express_number",//商家手动填写快递单号
    "order_deliver"=>"admin/Order/order_deliver", //已发货点击弹出的快递信息

    /*评价管理（未做）*/
    "evaluation_management"=>"admin/Evaluation/management",
    /*客户评价图片*/
    "evalution_imgs"=>"admin/Evaluation/evalution_imgs",
    /*评价审核操作*/
    'evalution_examine'=>"admin/Evaluation/evalution_examine",
    /*批量审核通过操作*/
    'evalution_all_check'=>"admin/Evaluation/evalution_all_check",
    /*退款维权(未做)*/
    "refund_rights"=>"admin/Refund/rights",
    /*晒单管理（未作）*/
    'order_sunburn'=>"admin/Order/sunburn",


    /*聊天管理*/
    "chat_index"=>"admin/Chat/index",
    /*后台获取用户发送过来的聊天信息*/
    "all_information"=>"admin/Chat/all_information",
    /*后台获取用户发送过来的聊天信息(已读)*/
    "read_all_information"=>"admin/Chat/read_all_information",
    /*后台获取用户发送过来的聊天信息（未读）*/
    "unread_all_information"=>"admin/Chat/unread_all_information",
    /*后台聊天信息的删除*/
    "chat_information_del"=>"admin/Chat/chat_information_del",
    /*批量删除*/
    "chat_information_deletes"=>"admin/Chat/chat_deletes",
    /*未读中按下回复按钮进入回复页面把状态值改变为已读*/
    "reading_information"=>"admin/Chat/reading_information",
    /*客服回复信息*/
    "admin_chat_push"=>"admin/Chat/admin_chat_push",


    /*内容管理*/
    "content_index"=>"admin/Content/index",
    "content_add"=>"admin/Content/add",
    "content_save"=>"admin/Content/save",
    "content_edit"=>"admin/Content/edit",
    "content_del"=>"admin/Content/del",
    "content_updata"=>"admin/Content/updata",


    /*常见问题*/
    "issue_index"=>"admin/Issue/index",
    "issue_add"=>"admin/Issue/add",
    "issue_save"=>"admin/Issue/save",
    "issue_edit"=>"admin/Issue/edit",
    "issue_del"=>"admin/Issue/del",
    "issue_updata"=>"admin/Issue/updata",
    "issue_status"=>"admin/Issue/status",
    "issue_putaway"=>"admin/Issue/putaway",


    /*客户中心*/
    "client_index"=>"admin/Client/index",


    /*售后维修*/
    "serve_index"=>"admin/Serve/index",
    "serve_status"=>"admin/Serve/status",
    "serve_reply"=>"admin/Serve/reply",



    /*广告管理*/
    "advertising_index"=>"admin/Advertising/index",
    "advertising_add"=>"admin/Advertising/add",
    "advertising_save"=>"admin/Advertising/save",
    "advertising_del"=>"admin/Advertising/del",
    "advertising_edit"=>"admin/Advertising/edit",
    "advertising_updata"=>"admin/Advertising/updata",
    "advertising_images"=>"admin/Advertising/images",


    /*电子保修卡*/
    "electron_index"=>"admin/Electron/index",
    "electron_save"=>"admin/Electron/save",
    "electron_add"=>"admin/Electron/add",
    "electron_del"=>"admin/Electron/del",


]);

Route::miss("public/miss");


