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

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用调试模式
    'app_debug'              => false,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => true,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [
        /*"__AdminCss__"=>"/static/admin/static"*/
        "__UPLOAD__"=>"/SiRing/public/upload",
        "__UPLOADS__"=>"/SiRing/public/uploads",
        "__UEDITOR__"=>"/SiRing/public/ueditor",
        "__TYPE__"=>"/SiRing/public/type",
        "__INDEX__"=>"/SiRing/public/",
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => true,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    //支付宝接口配置
    'alipay'=>[
        'appId'             => '2016112603335050',
        'gatewayUrl'        => 'https://openapi.alipay.com/gateway.do',
        'rsaPrivateKey'     => 'MIIEowIBAAKCAQEAz+SfWrndsOSD3AY3v5YtA9n+BoBcckMYfjgpIrT5Bu2YF2GR5oFCBJASSQeRRyDHPWL3i91lbyZeiBsE2l+rJcMTP+EfH6MpxMerwqfvOPw4p4OHHAnbI52xjdNZStBdIT7oEwEUsghuejCpWelL/b3CPFpW/1OpEVRnssw9gc0f1mius2eOXZ0+5JaJRZ/zJWxgyMHctF6NXcSG2oVOl0WyiNK/F4CuqdIcq1y8ZDiVvmRbyfzcEmbgob7MpwVFWw1Fge3z4fSnG7bicOJSXkPbWNhZmGe/yXCEXbA/8Kldp/nMkwnMGJ5A/3yFZTEUnmY60qnXA5T3R1KOnpXklwIDAQABAoIBAQCDq2VSbQ4AD3uES1vbuB3ipprBO2NR6zUEHEXReZWP0cPWazGhMJTDlww9vNFCn3wRYTEwIJUyBLcytQop1RXs4NS8TLUNsKWvwFcE/qABE54+WoukMonc0O+3x/hx7e5ONC2Ae9rDt5thQJjCHYTHvPvchcs8A5y9IRxcngcGweL6m6KUd4yT4yr5pPCXM8Q4B5cG/BM+MtLeqPJ1S7zheMKt4pN52M9pU9+n1V3nx1FgViv7ycOh8E+9L33S/Ri9HuLyIeV9zZ44g53ociUlSoQBnUIiDHWriHROWP0yxPdp0Et4oUPFcsDR1FVa8rFSmhZRauA6M7Um8SRXKVtBAoGBAPrhPqij8HfnOCJAGMcbwJnpQGZAypYBawEOSib3uIKyqEQmDlvzTjJgR2YbFUfGvgeAn0mX/Q4B/Vgffb1dqCJbU4McSE3GHJHCdBO6UqvUD4B8Qy6aJJomPGwgZAi+DAk9PtNDo2tC6DTZbd5UJMTqdpMq0776pjR6E3+7F2wZAoGBANQiyZTfw9qqf9xyQ4YKwu6v0165e+mnlycOTRkrBSESJUNSCH4aYHZnE4B9J1MU5fxxrZuk5qt6iu0N5AkUQY6xuLkKdjX8WJbHWgHHjvMxXsEqx1LQlQ2PSCCvF5jxB0xhzTjBa3uCzfabs3o+6MKh1QF1DuYMBE1B/rku8uwvAoGAdnuAIxbhj08EpLBOw2Ho8QdGocQBqRxcU7BS9tpRKnCDpUOvzl820/XCYodx4mcLAfINyCzelwn7gu3EbXVY3XjyFN57izd/8Jq8RUDeoEXTWGPXOqATnzVlnc8iTzqp5oclL5MnD5YWojb5e2GTx+fPPiuguvYXHnt00AMkyakCgYAkFOKqksDSUYu76Cd6BhyP0pImG3BrFplMCE+uxzVxIY/6+ln9cOkVWoTjpuXoaLaRkJhRz+N4KTi2B1XRAYQBDFN6DcB7gDdlNfUmNlYnIS+XtXn/qQChNMy02nMuDVkLcdshGyz37hCwMF1/nnGioToErG9jS4nzxhTYVJb2+wKBgGGU5XtXTZAeBtueAgwwPkdOe1pXHXjkeytG2cGeNJrBkMmj7B7eNt+3EkHw4yPgvj/e4OYNm4ojRH05FefZmb6dtLDUH0p1k9LeqEbGGbHn7cl7jDjTqaRznlODyaT3pJlRldZIaJ95VEwZtpMQCnItUAu5yGH3Vrgo2Y8eNpAn',
        'rsaPublicKey'      => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz+SfWrndsOSD3AY3v5YtA9n+BoBcckMYfjgpIrT5Bu2YF2GR5oFCBJASSQeRRyDHPWL3i91lbyZeiBsE2l+rJcMTP+EfH6MpxMerwqfvOPw4p4OHHAnbI52xjdNZStBdIT7oEwEUsghuejCpWelL/b3CPFpW/1OpEVRnssw9gc0f1mius2eOXZ0+5JaJRZ/zJWxgyMHctF6NXcSG2oVOl0WyiNK/F4CuqdIcq1y8ZDiVvmRbyfzcEmbgob7MpwVFWw1Fge3z4fSnG7bicOJSXkPbWNhZmGe/yXCEXbA/8Kldp/nMkwnMGJ5A/3yFZTEUnmY60qnXA5T3R1KOnpXklwIDAQAB',
        'alipayrsaPublicKey'=> 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz+SfWrndsOSD3AY3v5YtA9n+BoBcckMYfjgpIrT5Bu2YF2GR5oFCBJASSQeRRyDHPWL3i91lbyZeiBsE2l+rJcMTP+EfH6MpxMerwqfvOPw4p4OHHAnbI52xjdNZStBdIT7oEwEUsghuejCpWelL/b3CPFpW/1OpEVRnssw9gc0f1mius2eOXZ0+5JaJRZ/zJWxgyMHctF6NXcSG2oVOl0WyiNK/F4CuqdIcq1y8ZDiVvmRbyfzcEmbgob7MpwVFWw1Fge3z4fSnG7bicOJSXkPbWNhZmGe/yXCEXbA/8Kldp/nMkwnMGJ5A/3yFZTEUnmY60qnXA5T3R1KOnpXklwIDAQAB',
        'seller'            => '支付宝邮箱',//可不要
        'format'            => 'json',
        'charset'           => 'UTF-8',
        'signType'          => 'RSA2',
        'transport'         => 'https',
    ],
];
