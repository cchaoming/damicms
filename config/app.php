<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------
$tpl_404 = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . '404.tpl';
return [
    // 应用地址
    'app_host' => env('app.host', ''),
    // 应用的命名空间
    'app_namespace' => '',
    // 是否启用路由
    'with_route' => true,
    'app_express' => true,
// 默认应用
    'default_app' => 'home',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',
    // 应用映射（自动多应用模式有效）
    'app_map' => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind' => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list' => [],

    // 异常页面的模板文件
    'exception_tmpl' => app()->getThinkPath() . 'tpl/think_exception.tpl',
    '404_tmpl' => $tpl_404,
    'success_tmpl' => app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'success.tpl',
    'error_tmpl' => app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'success.tpl',//可以同success
    // 错误显示信息,非调试模式有效
    'error_message' => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg' => false,
    'http_exception_template' => [
        // 定义404错误的重定向页面地址
        404 => $tpl_404,
        // 还可以定义其它的HTTP status
        // 401 =>  APP_PATH.'401.html',
    ],
    'AP_EMAIL' => 'admin@126.com',
    'AP_PID' => '208800253045****',
    'AP_KEY' => '2c42yn37ykwg0m41us704r41dfcf****',
    'AP_TYPE' => '0',
    'LOCAL_REMOTE_PIC'=>0,
    'WX_APPID' => 'wxeb5a1b9ed655****',
    'WX_APPKEY' => '9b8a783685ce51324c41e9f4a20*****',
    'WX_TOKEN' => 'damicmstoken****',
    'WX_JQRKEY' => 'f4124fcc8feb767af3f90d63e1725597',
    'WX_TRADE' => '0',//是否开启微信支付
    'WX_LOGIN' => '0',//是否开启微信快捷登陆
    'QQ_APPID' => '',
    'QQ_APPKEY' => '',
    'QQ_LOGIN' => '0',
    'MOBILE_VERIFY' => '0',//手机短信验证码http://www.ihuyi.cn/
    'SMS_USER' => '',
    'SMS_PWD' => '',
    'MAIL_TRADE'=>0,
    'MAIL_REG'=>0,
    'MAIL_SMTP_SERVER'=>'smtp.163.com',
    'MAIL_FROM'=>'***@163.com',
    'MAIL_PASSSWORD'=>'bXBlaQ==',
    'MAIL_TOADMIN'=>'279197963@qq.com',
    'MAIL_PORT'=>25,
    'TRADE_TYPE' => [1 => ['code' => 'alipay', 'title' => '支付宝在线支付'], 5 => ['code' => 'wxpay', 'title' => '微信扫码支付'], 2 => ['code' => 'cod', 'title' => '货到付款'], 3 => ['code' => 'insite', 'title' => '站内扣款']],//4:在微信里直接是微信支付（所以缺少4）
    'TRADE_STATUS' => [0 => '等待付款', 1 => '已付款，等待发货', 2 => '已发货，等待收货', 3 => '已收货，交易完成', 4 => '申请换货', 5 => '换货处理中', 6 => '换货完成', 7 => '申请退货', 8 => '退货处理中', 9 => '退货完成', 10 => '交易关闭', 11 => '货到付款，等待发货'],
    'VERSION' => '7.0',
    'SERVER_URL' => 'http://www.damicms.com/',
    'TOKEN_NAME' => '__hash__',
    'app_map' => [
        'admin' => 'admin',  // 把admin应用映射为外部访问的key:think
    ],
];
