<?php
/*
qq登录 oauth V2.0
@author damicms.com 使用实例 
$config['appid']    = '';
$config['appkey']   = '';
$config['callback'] = '';
$o_qq = Oauth_qq::getInstance($config);
//then
$o_qq->login();
//or
$o_qq->callback();
$o_qq->get_openid();
$o_qq->get_user_info();*/
namespace until;
class Qqlogin
{
  private static $_instance;
  private $config = array();

  public function __construct($config)
  {
    $this->Oauth_qq($config);
  }
 
  public static function getInstance($config)
  {
    if(!isset(self::$_instance))
    {
      $c=__CLASS__;
      self::$_instance = new $c($config);
    }
    return self::$_instance;
  }
 
  private function Oauth_qq($config)
  {
    $this->config = $config;
    $_SESSION["appid"]    = $this->config['appid'];
    $_SESSION["appkey"]   = $this->config['appkey'];
    $_SESSION["callback"] = $this->config['callback'];
    $_SESSION["scope"] = "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
  }
 
  function login()
  {
    $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
    $login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
    . $_SESSION["appid"] . "&redirect_uri=" . urlencode($_SESSION["callback"])
    . "&state=" . $_SESSION['state']
    . "&scope=".$_SESSION["scope"];
    header("Location:$login_url");
  }
 
  function callback()
  {
    if($_REQUEST['state'] == session('state')) //csrf
    {
      $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
      . "client_id=" . session("appid"). "&redirect_uri=" . urlencode(session("callback"))
      . "&client_secret=" . session("appkey"). "&code=" . $_REQUEST["code"];
 
      $response = get_url_contents($token_url);
      if (strpos($response, "callback") !== false)
      {
        $lpos = strpos($response, "(");
        $rpos = strrpos($response, ")");
        $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
        $msg = json_decode($response);
        if (isset($msg->error))
        {
          echo "<h3>error:</h3>" . $msg->error;
          echo "<h3>msg  :</h3>" . $msg->error_description;
          exit;
        }
      }
 
      $params = array();
      parse_str($response, $params);
 
      session("access_token", $params["access_token"]);
    }
    else
    {
      echo("The state does not match. You may be a victim of CSRF.");
    }
  }
 
  function get_openid()
  {
    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token="
    . session('access_token');
 
    $str  = get_url_contents($graph_url);
    if (strpos($str, "callback") !== false)
    {
      $lpos = strpos($str, "(");
      $rpos = strrpos($str, ")");
      $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
    }
 
    $user = json_decode($str);
    if (isset($user->error))
    {
      //echo "<h3>error:</h3>" . $user->error;
      //echo "<h3>msg  :</h3>" . $user->error_description;
	  return '';
      exit;
    }
 
    //set openid to session
    return session("openid", $user->openid);
  }
 
  function get_user_info()
  {
    $get_user_info = "https://graph.qq.com/user/get_user_info?"
    . "access_token=" . session('access_token')
    . "&oauth_consumer_key=" . session("appid")
    . "&openid=" . session("openid")
    . "&format=json";
 
    $info = get_url_contents($get_user_info);
    $arr = json_decode($info, true);
 
    return $arr;
  }
 
  public function __clone()
  {
    trigger_error('Clone is not allow' ,E_USER_ERROR);
  }
 
}