<?php
/*
微信登录 oauth V2.0
@author damicms.com 使用实例 
$config['appid']    = '';
$config['appsecret']   = '';
$config['callback'] = '';
*/
namespace  until;
class Wxlogin
{
    private static $_instance;
    private $config = array();

    public function __construct($config)
    {
        $this->wx_set($config);
    }

    public static function getInstance($config)
    {
        if (!isset(self::$_instance)) {
            $c = __CLASS__;
            self::$_instance = new $c($config);
        }
        return self::$_instance;
    }

    private function wx_set($config)
    {
        $this->config = $config;
        $_SESSION["wx_appid"] = $this->config['appid'];
        $_SESSION["wx_appsecret"] = $this->config['appsecret'];
        $_SESSION["wx_callback"] = urlencode($this->config['callback']);
    }

    function login()
    {
        $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
        //$login_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$_SESSION['wx_appid'].'&redirect_uri='.$_SESSION['wx_callback'].'&response_type=code&scope=snsapi_userinfo&state='.$_SESSION['state'].'&connect_redirect=1#wechat_redirect';
        $login_url = "https://open.weixin.qq.com/connect/qrconnect?appid=".$_SESSION['wx_appid']."&redirect_uri={$_SESSION['wx_callback']}&response_type=code&scope=snsapi_login&state={$_SESSION['state']}";
		header("Location:$login_url");
    }

    function get_user_info()
    {
        if ($_REQUEST['state'] == $_SESSION['state']) {
            $code = $_GET['code'];
            if (empty($code)) {
                echo '获取授权失败';
                exit();
            }
            $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $_SESSION['wx_appid'] . '&secret=' . $_SESSION['wx_appsecret'] . '&code=' . $code . '&grant_type=authorization_code';
            $token = json_decode(get_url_contents($token_url));
            if (isset($token->errcode)) {
                echo '<h1>错误：</h1>' . $token->errcode;
                echo '<br/><h2>错误信息：</h2>' . $token->errmsg;
                exit;
            }
            $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $_SESSION['wx_appid'] . '&grant_type=refresh_token&refresh_token=' . $token->refresh_token;
            $access_token = json_decode(get_url_contents($access_token_url));
            if (isset($access_token->errcode)) {
                echo '<h1>错误：</h1>' . $access_token->errcode;
                echo '<br/><h2>错误信息：</h2>' . $access_token->errmsg;
                exit;
            }
            $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token->access_token . '&openid=' . $access_token->openid . '&lang=zh_CN';
//转成对象
            $user_info = json_decode(get_url_contents($user_info_url));
            if (isset($user_info->errcode)) {
                echo '<h1>错误：</h1>' . $user_info->errcode;
                echo '<br/><h2>错误信息：</h2>' . $user_info->errmsg;
                exit;
            }
            return $user_info;
        } else {
            echo("The state does not match . You may be a victim of CSRF . ");
        }
    }

    public function __clone()
    {
        trigger_error('Clone is not allow', E_USER_ERROR);
    }

}

/* 公用函数 */
if (!function_exists("do_post")) {
    function do_post($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);

        curl_close($ch);
        return $ret;
    }
}

if (!function_exists("get_url_contents")) {
    function get_url_contents($url)
    {
        if (ini_get("allow_url_fopen") == "1")
            return file_get_contents($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
?>