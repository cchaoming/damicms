<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$
namespace app\home\controller;
class Wx extends Base{
// 首页
    public function index()
    {
        header("Content-type:text/html;charset=utf-8");
        define("APP_ID", config("app.WX_APPID"));//微信公众平台的AppID
        define("APP_KEY", config("app.WX_APPKEY"));//微信公众平台的密码AppSecret
        define('URL_TOKEN',config("app.WX_TOKEN"));
        define('JQR_KEY',config("app.WX_JQRKEY"));//图灵机器人key 官网:http://www.tuling123.com申请
        define('DEBUG', false);//是否开启调试模式
        $weixin = new \until\Weixin(APP_ID,APP_KEY,DEBUG);
        if(intval(S('wx_validate')) !=1){
//首次验证url开启下面几句
            $weixin->url_token = URL_TOKEN;
            $weixin->valid();
            S('wx_validate',1,0);
            exit();
        }
        if($weixin->access_token !== false){
            if(intval(S('is_menu')) !=1){
//生成自定义菜单,子菜单最多支持5个演示下微新自定义菜单
                $list = M('wx_menu')->whereRaw('pid=0')->orderRaw('id asc')->select()->toArray();
                foreach($list as $k=>$v){
                    if($v['menu_type'] != 2){
//无子菜单仅仅只一级
                        if($v['menu_type'] ==0){
                            $url= (strpos($v['menu_value'],'http:') === false)?('http://'.$_SERVER['HTTP_HOST'].$v['menu_value']):$v['menu_value'];
                            $ret[] = array('type'=>'view','name'=>$v['menu_name'],'url'=>$url);
                        }
                        else if($v['menu_type']==1){
                            $ret[] = array('type'=>'click','name'=>$v['menu_name'],'key'=>'v'.$v['id']);
                        }
                    }else{
                        $sublist = M('wx_menu')->whereRaw('pid='.$v['id'])->orderRaw('id asc')->select()->toArray();
                        $sub = array();
                        foreach($sublist as $key=>$value){
                            if($value['menu_type'] ==0){
                                $url= (strpos($value['menu_value'],'http:') === false)?('http://'.$_SERVER['HTTP_HOST'].$value['menu_value']):$value['menu_value'];
                                $sub[] = array('type'=>'view','name'=>$value['menu_name'],'url'=>$url);
                            }else if($value['menu_type'] == 1){
                                $sub[] = array('type'=>'click','name'=>$value['menu_name'],'key'=>'v'.$value['id']);
                            }
                        }
                        $ret[] = array('name'=>$v['menu_name'],'sub_button'=>$sub);
                    }
                }
                arrayRecursive($ret, 'urlencode', true);
                $xjson = json_encode(array('button'=>$ret));
                $xjson = urldecode($xjson);
                $weixin ->create_menu($xjson);
                S('is_menu',1,0);
            }
            /*生成自定义菜单结束*/
            $weixin->getMsg();
            $type = $weixin->msgtype; //消息类型
            $username = $weixin->msg['FromUserName'];//哪个用户给你发的消息,这个$username是微信加密之后的，每个用户都是一一对应的
            if ($type === 'event') {//点击菜单事件
                $eventkey = $weixin->eventkey;//获取当前菜单key
                if($eventkey=="subscribe"){
                    $relay = $weixin->makeText('欢迎使用大米CMS，官网:www.damicms.com');
                }else{
                    $id = intval(substr($eventkey,1));
                    $keyword = M('wx_menu')->removeOption()->whereRaw('id='.$id)->value('menu_value');
                    if($keyword != '') {
                        if ($keyword == '绑定微信' || $keyword == '微信登陆' ) {
                            $Data['content'] = '会员绑定微信号';
                            $Data['items'][0]['title'] = '绑定您的微信号';
                            $Data['items'][0]['description'] = '点击图片开始绑定您的微信号，以后就可以微信直接登录了哦';
                            $Data['items'][0]['picUrl'] = 'http://' . $_SERVER ['HTTP_HOST'] . '/' . __ROOT__ . '/Public/image/bindwx.png';
                            $Data['items'][0]['url'] = 'http://' . $_SERVER ['HTTP_HOST'] . '/' . __ROOT__ . U('Member/bindwx?openid='.$username);
                            $reply = $weixin->makeNews(Data);
                        } else {
                            //获取所有产品分类
                            $types = get_children(22);
                            $newsData['content'] = '微产品展览会';
                            $list = M('article')->whereRaw("title like '%{$keyword}%' and status=1")->limit(10)->select()->toArray();
                            foreach($list as $key=>$value){
                                $newsData['items'][$key]['title'] = $value['title'];
                                $newsData['items'][$key]['description'] = $value['description'];
                                $newsData['items'][$key]['picUrl'] = 'http://'.$_SERVER ['HTTP_HOST'].'/'.__ROOT__.ret_pic($value['imgurl']);
                                $newsData['items'][$key]['url'] = 'http://'.$_SERVER ['HTTP_HOST'].url('articles',$value['aid']);
                            }
                            $reply = $weixin->makeNews($newsData);
                        }
                    }
                }
            }
            if ($type === 'text') {//文本输入
                $kwds=$weixin->msg['Content'];
                $res=M('article')->whereRaw("title like '%{$kwds}%'")->find();
                if($res) {
                    $reply = $weixin->makeText($res['title']);
                }
                else{
                    $jqr_url =  'http://www.tuling123.com/openapi/api?key='.JQR_KEY.'&info='.$kwds;
                    $jqr_answer = get_url_contents($jqr_url);
                    $jqr_arr = json_decode($jqr_answer,true);
                    if(isset($jqr_arr['text']) && $jqr_arr['text'] !=''){
                        $reply = $weixin->makeText($jqr_arr['text']);
                    }else{
                        $reply = $weixin->makeText('抱歉，根据您输入的文本，暂时未找到相关匹配信息');
                    }
                }
            }
            if ($type === 'voice') {//语音输入
                $kwds = substr($weixin->msg['Recognition'],0,-3);
                $res=M('article')->whereRaw("title like '%{$kwds}%'")->find();
                if($res) {
                    $reply = $weixin->makeText($res['title']);
                }
                else{
                    $reply = $weixin->makeText('抱歉，根据您输入的语音，暂时未找到相关匹配信息');
                }
            }
            $weixin->reply($reply);
        }else
        {
            echo '获取access_token失败!';exit();
        }
    }

}
?>