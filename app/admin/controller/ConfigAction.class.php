<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 系统全局配置

    @Filename ConfigAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-25 20:26:12 $
*************************************************************/
class ConfigAction extends CommonAction
{	
	public function index()
	{
		$type = M('config');
		$list = $type->where('id=1')->find();
		$this->assign('list',$list);
		$this->display('index');
	} 
	public function update()
	{
		$data['sitetitle'] = trim($_POST['sitetitle']); 
		$data['sitetitle2'] = trim($_POST['sitetitle2']); 
		$data['siteurl'] = trim($_POST['siteurl']); 
		$data['updown'] = trim($_POST['updown']); 
		$data['xgwz'] = trim($_POST['xgwz']); 
		$data['sitedescription'] = trim($_POST['sitedescription']); 
		$data['sitekeywords'] = trim($_POST['sitekeywords']); 
		$data['sitelx'] = trim($_POST['sitelx']); 
		$data['sitetcp'] = trim($_POST['sitetcp']); 
		$data['sitelang'] = trim($_POST['sitelang']); 
		$data['watermark'] = trim($_POST['watermark']); 
		$data['watermarkimg'] = trim($_POST['watermarkimg']);
		$data['sitetpl'] = trim($_POST['sitetpl']);
		$data['waptpl'] = trim($_POST['waptpl']);
		$data['indexrec'] = trim($_POST['indexrec']); 
		$data['indexhot'] = trim($_POST['indexhot']);
		$data['indexlink'] = trim($_POST['indexlink']); 
		$data['indexpic'] = trim($_POST['indexpic']); 
		$data['noticeid'] = trim($_POST['noticeid']); 
		$data['noticenum'] = trim($_POST['noticenum']); 
		$data['isping'] = trim($_POST['isping']); 
		$data['pingoff'] = trim($_POST['pingoff']); 
		$data['bookoff'] = trim($_POST['bookoff']); 
		$data['mood'] = trim($_POST['mood']); 
		$data['sitelogo'] = trim($_POST['sitelogo']); 
		$data['ishits'] = trim($_POST['ishits']); 
		$data['iscopyfrom'] = trim($_POST['iscopyfrom']); 
		$data['isauthor'] = trim($_POST['isauthor']); 
		$data['indexvote'] = trim($_POST['indexvote']); 
		$data['ishomeimg'] = trim($_POST['ishomeimg']); 
		$data['mouseimg'] = trim($_POST['mouseimg']);
		$data['iszy'] = trim($_POST['iszy']);
		$data['id'] = trim($_POST['id']); 
		$data['artlistnum'] = trim($_POST['artlistnum']); 
		$data['artlisthot'] = trim($_POST['artlisthot']); 
		$data['artlistrec'] = trim($_POST['artlistrec']); 
		$data['articlerec'] = trim($_POST['articlerec']); 
		$data['articlehot'] = trim($_POST['articlehot']); 
		$data['rollnum'] = trim($_POST['rollnum']); 
		$data['postovertime'] = trim($_POST['postovertime']); 
		$data['urlmode'] = trim($_POST['urlmode']); 
		$data['flashmode'] = trim($_POST['flashmode']); 
		$data['indexnoticetitle'] = trim($_POST['indexnoticetitle']);
		$data['indexrectitle'] = trim($_POST['indexrectitle']);
		$data['indexhottitle'] = trim($_POST['indexhottitle']);
		$data['indexvotetitle'] = trim($_POST['indexvotetitle']);
		$data['indexpictitle'] = trim($_POST['indexpictitle']);
		$data['indexpicnum'] = trim($_POST['indexpicnum']); 
		$data['indexpicscroll'] = trim($_POST['indexpicscroll']); 
		$data['indexlinktitle'] = trim($_POST['indexlinktitle']);
		$data['indexlinkimg'] = trim($_POST['indexlinkimg']);
		$data['indexdiylink'] = trim($_POST['indexdiylink']);
		$data['listrectitle'] = trim($_POST['listrectitle']);
		$data['listhottitle'] = trim($_POST['listhottitle']);
		$data['listshowmode'] = trim($_POST['listshowmode']);
		$data['artrectitle'] = trim($_POST['artrectitle']);
		$data['arthottitle'] = trim($_POST['arthottitle']);
		$data['suffix'] = trim($_POST['suffix']); 
		$data['is_build_html'] = intval($_POST['is_build_html']);
		$data['istrade'] = intval($_POST['istrade']);
		$data['isread'] = intval($_POST['isread']);
		$data['defaultup'] = intval($_POST['defaultup']);
		$data['defaultmp'] = intval($_POST['defaultmp']);
		$data['islocalpic'] = intval($_POST['islocalpic']);
		$data = array_map('remove_xss',$data);
		switch($data['suffix'])
		{
			case 0:
				$resuffix = '.html';
				break;
			case 1:
				$resuffix = '.htm';
				break;
			case 2:
				$resuffix = '.shtml';
				break;
			case 3:
				$resuffix = '.php';
				break;
			case 4:
				$resuffix = '.asp';
				break;
			case 5:
				$resuffix = '.aspx';
				break;
			case 6:
				$resuffix = '.jsp';
				break;
		}
		$type = M('config');
		$config = F('basic','','./Web/Conf/');
		switch($config['suffix'])
		{
			case 0:
				$suffix = '.html';
				break;
			case 1:
				$suffix = '.htm';
				break;
			case 2:
				$suffix = '.shtml';
				break;
			case 3:
				$suffix = '.php';
				break;
			case 4:
				$suffix = '.asp';
				break;
			case 5:
				$suffix = '.aspx';
				break;
			case 6:
				$suffix = '.jsp';
				break;
		}
		unset($config);
		$result = $type->data($data)->save(); 
		//重写缓存
			F('basic',$data,'./Web/Conf/');
		//url模式处理
			$path = './Web/Conf';
			
			if($data['urlmode'] == 0)
			{
				$fp = fopen($path."/config.php","r");
				$configStr = fread($fp,filesize($path."/config.php"));
				fclose($fp);				
				$configStr = preg_replace("/'URL_MODEL'=>[0-9]/","'URL_MODEL'=>1",$configStr);
				$configStr = preg_replace("/'URL_MODEL'=>[0-9]/","'URL_MODEL'=>1",$configStr);
				$configStr = preg_replace("/'IS_BUILD_HTML'=>[0-9]/","'IS_BUILD_HTML'=>".intval($_POST['is_build_html']),$configStr);
				$configStr = preg_replace("/'DEFAULT_THEME'=>'.*'/", "'DEFAULT_THEME'=>'" . htmlspecialchars($data['sitetpl'],ENT_QUOTES) . "'", $configStr);
				$configStr = preg_replace("/'DEFAULT_WAP_THEME'=>'.*'/", "'DEFAULT_WAP_THEME'=>'" . htmlspecialchars($data['waptpl'],ENT_QUOTES) . "'", $configStr);
				$configStr = str_replace("=>'".$suffix."'","=>'".$resuffix."'",$configStr);
				$configStr = str_replace('index.php?s=','index.php/',$configStr);				
				@chmod($path,0777);
				$fp = fopen($path."/config.php","w") or die("<script>alert('写入配置失败，请检查../Web/Conf目录是否可写入！');history.go(-1);</script>");
				fwrite($fp,$configStr);
				fclose($fp);
			}
			elseif($data['urlmode'] == 1)
			{
				$fp = fopen($path."/config.php","r");
				$configStr = fread($fp,filesize($path."/config.php"));
				fclose($fp);
				$configStr = preg_replace("/'URL_MODEL'=>[0-9]/","'URL_MODEL'=>2",$configStr);
				$configStr = preg_replace("/'IS_BUILD_HTML'=>[0-9]/","'IS_BUILD_HTML'=>".intval($_POST['is_build_html']),$configStr);
				$configStr = preg_replace("/'DEFAULT_THEME'=>'.*'/", "'DEFAULT_THEME'=>'" . htmlspecialchars($data['sitetpl'],ENT_QUOTES) . "'", $configStr);
				$configStr = preg_replace("/'DEFAULT_WAP_THEME'=>'.*'/", "'DEFAULT_WAP_THEME'=>'" . htmlspecialchars($data['waptpl'],ENT_QUOTES) . "'", $configStr);
				$configStr = str_replace("=>'".$suffix."'","=>'".$resuffix."'",$configStr);
				$configStr = str_replace('index.php?s=','index.php/',$configStr);
				@chmod($path,0777);
				$fp = fopen($path."/config.php","w") or die("<script>alert('写入配置失败，请检查../Web/Conf目录是否可写入！');history.go(-1);</script>");
				fwrite($fp,$configStr);
				fclose($fp);
			}
			else
			{
				$fp = fopen($path."/config.php","r");
				$configStr = fread($fp,filesize($path."/config.php"));
				fclose($fp);
				$configStr = preg_replace("/'URL_MODEL'=>[0-9]/","'URL_MODEL'=>3",$configStr);
				$configStr = preg_replace("/'IS_BUILD_HTML'=>[0-9]/","'IS_BUILD_HTML'=>".intval($_POST['is_build_html']),$configStr);
				$configStr = preg_replace("/'DEFAULT_THEME'=>'.*'/", "'DEFAULT_THEME'=>'" . htmlspecialchars($data['sitetpl'],ENT_QUOTES) . "'", $configStr);
				$configStr = preg_replace("/'DEFAULT_WAP_THEME'=>'.*'/", "'DEFAULT_WAP_THEME'=>'" . htmlspecialchars($data['waptpl'],ENT_QUOTES) . "'", $configStr);
				$configStr = str_replace("=>'".$suffix."'","=>'".$resuffix."'",$configStr);
				$configStr = str_replace('index.php/','index.php?s=',$configStr);
				@chmod($path,0777);
				$fp = fopen($path."/config.php","w") or die("<script>alert('写入配置失败，请检查../Web/Conf目录是否可写入！');history.go(-1);</script>");
				fwrite($fp,$configStr);
				fclose($fp);
			}
			//网站语言
			/*if($data['sitelang'] == 0)
			{
				$path = './Web/Tpl/'.$data['sitetpl'].'/js/';
				$fp = fopen($path."/language.js","r");
				$configStr = fread($fp,filesize($path."/language.js"));
				fclose($fp);
				$configStr = str_replace('Default_isFT=1','Default_isFT=0',$configStr);
				@chmod($path,0777);
				$fp = fopen($path."/language.js","w") or die("<script>alert('写入配置失败，请检查".$path."目录是否可写入！');history.go(-1);</script>");
				fwrite($fp,$configStr);
				fclose($fp);
			}
			if($data['sitelang'] == 1)
			{
				$path = './Web/Tpl/'.$data['sitetpl'].'/js/';
				$fp = fopen($path."/language.js","r");
				$configStr = fread($fp,filesize($path."/language.js"));
				fclose($fp);
				$configStr = str_replace('Default_isFT=0','Default_isFT=1',$configStr);
				@chmod($path,0777);
				$fp = fopen($path."/language.js","w") or die("<script>alert('写入配置失败，请检查".$path."目录是否可写入！');history.go(-1);</script>");
				fwrite($fp,$configStr);
				fclose($fp);
			}*/
						//保存邮件配置
	$config_file = "./Public/Config/config.ini.php";
	$fp = fopen($config_file,"r");
	$configStr = fread($fp,filesize($config_file));
	fclose($fp);
	$configStr = preg_replace("/'LOCAL_REMOTE_PIC'=>[0-9]/","'LOCAL_REMOTE_PIC'=>".$data['islocalpic'],$configStr);
	$configStr = preg_replace("/'MAIL_TRADE'=>.*.,/","'MAIL_TRADE'=>".htmlspecialchars($_POST['MAIL_TRADE'],ENT_QUOTES).",",$configStr);
	$configStr = preg_replace("/'MAIL_REG'=>.*.,/","'MAIL_REG'=>".htmlspecialchars($_POST['MAIL_REG'],ENT_QUOTES).",",$configStr);
	$configStr = preg_replace("/'MAIL_SMTP_SERVER'=>'.*'/","'MAIL_SMTP_SERVER'=>'".htmlspecialchars($_POST['MAIL_SMTP_SERVER'],ENT_QUOTES)."'",$configStr);
	$configStr = preg_replace("/'MAIL_FROM'=>'.*'/","'MAIL_FROM'=>'".htmlspecialchars($_POST['MAIL_FROM'],ENT_QUOTES)."'",$configStr);	
	if(C('MAIL_PASSSWORD') != $_POST['MAIL_PASSSWORD']){$configStr = preg_replace("/'MAIL_PASSSWORD'=>'.*'/","'MAIL_PASSSWORD'=>'".dami_encrypt($_POST['MAIL_PASSSWORD'])."'",$configStr);}	
	$configStr = preg_replace("/'MAIL_TOADMIN'=>'.*'/","'MAIL_TOADMIN'=>'".htmlspecialchars($_POST['MAIL_TOADMIN'],ENT_QUOTES)."'",$configStr);
	$configStr = preg_replace("/'MAIL_PORT'=>.*.,/","'MAIL_PORT'=>".htmlspecialchars($_POST['MAIL_PORT'],ENT_QUOTES).",",$configStr);
	$fp = fopen($config_file,"w") or die("<script>alert('写入配置失败，请检查安装目录/Public/Config/config.ini.php是否可写入！');history.go(-1);</script>");	
	fwrite($fp,$configStr);
	fclose($fp);
	//清理缓存
	$Webpath = './Web/Runtime/';
	$Adminpath = './Admin/Runtime/';
		if(is_dir($Webpath))
		{
			@deldir($Webpath);
		}
		if(is_dir($Adminpath))
		{
			@deldir($Adminpath);
		}
			$this->assign("jumpUrl",U('Config/index'));
			$this->success('操作成功!');	
		
	}
}
?>