<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 后台框架

    @Filename IndexAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:18:41 $
*************************************************************/
class IndexAction extends CommonAction
{	
//首页显示框架主体index
    public function index()
    {
       $this->display('index');
    }
	
//首页显示框架内left页面
	public function left()
    {
		
		$sql ="select role.id as role_id,node.* from dami_role as role,dami_role_admin as user,dami_access as access,dami_node as node where user.user_id=".$_SESSION['authId']." and user.role_id=role.id and role.status=1 and role.id=access.role_id and access.node_id = node.id and node.ismenu=1 and menu_pid=37";
		$list = M()->query($sql);
		//栏目菜单一二级
		foreach($list as $k=>$v){
			$submenu  = M('node')->where('menu_pid='.$v['id'])->select();
			foreach($submenu as $x=>$y){
			if($y['level']==2){
			$submenu[$x]['url'] = U($y['name'].'/index');	
			}
			else if($y['level']==3){
			$model_name = M('node')->where('id='.$y['pid'])->getField('name');
			$submenu[$x]['url'] = U($model_name.'/'.$y['name']);
			}
			}
			$list[$k]['submenu'] = $submenu;
		}
		//dump($list);
		$this->assign('admin_menu',$list);
		if($_SESSION[C('ADMIN_AUTH_KEY')]){
		$this->display('left_super');
		}
		else{
        $this->display('left_common');
		}
    }
	
//首页显示框架内head头部页面
	public function head()
    {
        $this->display('head');
    }
//首页显示框架内bottom底部页面
	public function bottom()
    {
        $this->display('bottom');
    }
//首页显示框架内center页面包含了left和main
	public function center()
    {
        $this->display('center');
    }
//首页显示框架内右侧主页面
	public function main()
    {
	    $count = array();
		$article = M('article');
		$type = M('type');
		$link = M('link');
		$hd = M('flash');
		$ping = M('pl');
		$guest = M('guestbook');
		$vip = M('vip_mess')->find();
if($vip){
$url = C('SERVER_URL')."/Public/check_webvip?vip_name=".$vip['vip_name']."&vip_pwd=".$vip['vip_pwd'];
$res = json_decode(get_url_contents($url));
if($res->status ==0){
$vip_mess='<font color="#FF0000">ㄨ您使用的是大米CMS未进行商业授权，请&nbsp;<a href="http://www.damicms.com/Public/donate">尽快购买</a>&nbsp;<a href="__APP__/Apk/vip_config">设置授权码</a></font>';
}
else
{
$vip_mess='<font color="#006600">√ 恭喜你，您已获得大米CMS商业使用授权，更多技术参考&nbsp;<a href="http://www.damicms.com/">大米CMS官网</a>！</font>';}
}else
{
$vip_mess='<font color="#FF0000">ㄨ您使用的是大米CMS未进行商业授权，请&nbsp;<a href="http://www.damicms.com/Public/donate">尽快购买</a>&nbsp;<a href="__APP__/Apk/vip_config">设置授权码</a></font>';
}
$this->assign('vip_mess',$vip_mess);	
	//文章总数
		$count['article'] = $article->count();
	//未审核文章总数
		$count['narticle'] = $article->where('status=0')->count();
	//留言总数	
		$count['guestbook'] = $guest->count();
	//未审核留言总数	
		$count['nguestbook'] = $guest->where('status=0')->count();
	//栏目总数	
		$count['type'] = $type->count();
	//链接总数
		$count['link'] = $link->count();
	//幻灯总数	
		$count['hd'] = $hd->count();
	//评论总数
		$count['ping'] = $ping->count();
	//未审核评论
		$count['nping'] = $ping->where('status=0')->count();
        $this->assign('count',$count);
		unset($article,$type,$link,$hd,$ping,$guest);
		$info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time').'秒',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s",time() + 8 * 3600),
            '服务器域名/IP' => $_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)),2).'M',
            'register_globals' => get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime())?'YES':'NO',
            );
        $this->assign('info',$info);
		$this->display('main');
    }
//底部版权公共页
	public function copy()
	{
		$this->display('copy');
	}
}
?>