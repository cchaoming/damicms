<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 文件上传

    @Filename FileAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:11:22 $
*************************************************************/
namespace app\admin\controller;

use app\BaseController;
use until\Image;
use until\UploadFile;
const __PUBLIC__ = '/public';
class File extends Damicms{
	//默认上传幻灯
	public function hd()
	{
		return $this->display('hd');
	}
	//上传logo
	public function logo()
	{
		return $this->display('logo');
	}
	//上传水印图
	public function watermark()
	{
		return $this->display('watermark');
	}
	//上传广告图片
	public function ad()
	{
		return $this->display('ad');
	}
	//上传友情图片
	public function link()
	{
		return $this->display('link');
	}
	//上传图片缩略图
	public function thumb()
	{
		return $this->display('thumb');
	}
	//上传附件
	public function attach()
	{
		return $this->display('attach');
	}
	//上传幻灯图片
	public function upload()
	{
		return $this->upmethod('up','c');
	}

	//上传logo图片
	public function uploadlogo()
	{
		return $this->upmethod('uplogo','l');
	}
	//上传广告图片
	public function uploadad()
	{
		return $this->upmethod('upad','a');
	}
	//上传友情链接图片
	public function uploadlink()
	{
		return $this->upmethod('uplink','k');
	}
	//上传缩略图
	public function uploadthumb()
	{
		return $this->upmethod('upthumb','t');
	}
	//上传附件
	public function uploadattach()
	{
	     return $this->upmethod('upattach','at');
	}
	//上传水印图片
	public function uploadwatermark()
	{
		return $this->upmethod('upwatermark','w');
	}

	//幻灯代码处理
	private function c($data)
	{
		$js='';
		if(!empty($data[0]['savename']))
		{
			$js.="<script language=javascript>parent.document.myform.Pic.value='".$data[0]['savename']."';</script>";
			$this->assign('js',$js);
			return true;
		}
		else
		{
			return false;
		}
	}
	//logo代码处理
	private function l($data)
	{
		$js='';
		if(!empty($data[0]['savename']))
		{
			$js.="<script language=javascript>parent.frm.oSiteLogo.value='".$data[0]['savename']."';</script>";
			$this->assign('js',$js);
			return true;
		}
		else
		{
			return false;
		}
	}
	//水印代码处理
	private function w($data)
	{
		$js='';
		if(!empty($data[0]['savename']))
		{
			$js.="<script language=javascript>parent.frm.owatermarkimg.value='".$data[0]['savename']."';</script>";
			$this->assign('js',$js);
			return true;
		}
		else
		{
			return false;
		}
	}
	//广告代码处理
	private function a($data)
	{
		if(!empty($data[0]['savename']))
		{
			$js='';
			$js.="<script language=javascript>parent.KE.insertHtml('<img src=\"".__PUBLIC__."/Uploads/ad/".$data[0]['savename']."\"/>');</script>";
			$this->assign('js',$js);
			return true;
		}
		else
		{
			return false;
		}
	}
	//友链代码处理
	private function k($data)
	{
		$js='';
		if(!empty($data[0]['savename']))
		{
			$js.="<script language=javascript>parent.document.myform.LogoUrl.value='".__PUBLIC__."/Uploads/link/{$data[0]['savename']}';</script>";
			$this->assign('js',$js);
			return true;
		}
		else
		{
			return false;
		}
	}
	//缩略图代码处理
	private function t($data)
	{
		$js='';
		if(!empty($data[0]['savename']))
		{
			$js.="<script language=javascript>parent.document.myform.Images.value='/Public/Uploads/thumb/{$data[0]['savename']}';</script>";
			$js.="<script language=javascript>parent.document.myform.Useimg.checked=true;</script>";
			//$js.="<script language=javascript>parent.KE.insertHtml('<div align=\"center\"><img src=\"".__PUBLIC__."/Uploads/thumb/thumb_{$data[0]['savename']}\"/></div>');</script>";
			$this->assign('js',$js);
			return true;
		}
		else
		{
			return false;
		}
	}
	//附件代码处理
	private function at($data)
	{
   // var_dump($data);exit();
		$js='';
		if(!empty($data[0]['savename']))
		{
			switch($data[0]['extension'])
			{
				case 'zip':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/zip.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'tar.gz':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/zip.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case '7z':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/zip.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'rar':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/rar.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'doc':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/doc.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'docx':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/doc.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'ppt':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/ppt.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'pptx':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/ppt.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'cls':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/cls.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'clsx':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/cls.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'txt':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/txt.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'pdf':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/pdf.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'swf':
					$js.="<script language=javascript>parent.KE.insertHtml('<br>附件下载:<img src=\"".__PUBLIC__."/Editor/mini/swf.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				default:
					$js.="<script language=javascript>parent.KE.insertHtml('<img src=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\" />');</script>";
				//default 默认为gif,png,jpg等图片
			}
			$this->assign('js',$js);
			return true;
		}
		else
		{
			return false;
		}
	}


	//*********************以下是执行上传的方法**************************

	//幻灯上传方法
	private function up()
	{
		$upload=new UploadFile();
		$upload->maxSize='20480000';  
		$upload->savePath='./Public/Uploads/hd/';
		$upload->saveRule= 'time';
		$upload->uploadReplace=true;     
		$upload->allowExts=array('jpg','jpeg','png','gif');     //准许上传的文件后缀
		$upload->allowTypes=array('image/jpeg','image/pjpeg','image/png','image/gif','image/x-png');//准许上传的文件类型
		if($upload->upload())
		{
			$info=$upload->getUploadFileInfo();
			$config = F('basic','','./Web/Conf/');
			if($config['watermark'] == 1)
			{
			import('ORG.Util.Image');
			Image::water($info[0]['savepath'].$info[0]['savename'], './Public/Uploads/logo/'.$config['watermarkimg']);
			}
			return $info;
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}
	//logo上传方法
	private function uplogo()
	{
		$upload=new UploadFile();
		$upload->maxSize='20480000';  
		$upload->savePath='./Public/Uploads/logo/';
		$upload->saveRule='logo_'.date('YmdHis');   
		$upload->uploadReplace=true;     
		$upload->allowExts=array('jpg','jpeg','png','gif');     //准许上传的文件后缀
		$upload->allowTypes=array('image/jpeg','image/pjpeg','image/png','image/gif','image/x-png');//准许上传的文件类型
		$upload->autoSub=false; //是否使用子目录进行保存上传文件
		if($upload->upload())
		{
			$info=$upload->getUploadFileInfo();
			return $info;
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}

	//水印上传方法
	private function upwatermark()
	{
		$upload=new UploadFile();
		$upload->maxSize='20480000';  
		$upload->savePath='./Public/Uploads/logo/';
		$upload->saveRule='watermark_'.date('YmdHis');   
		$upload->uploadReplace=true;     
		$upload->allowExts=array('jpg','jpeg','png','gif');     //准许上传的文件后缀
		$upload->allowTypes=array('image/jpeg','image/pjpeg','image/png','image/gif','image/x-png');//准许上传的文件类型
		$upload->autoSub=false; //是否使用子目录进行保存上传文件
		if($upload->upload())
		{
			$info=$upload->getUploadFileInfo();
			return $info;
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}
	private function upad()
	{
		$upload=new UploadFile();
		$upload->maxSize= '20480000';  
		$upload->savePath= './Public/Uploads/ad/';
		$upload->saveRule= 'time';
		$upload->uploadReplace= true;     
		$upload->allowExts= array('jpg','jpeg','png','gif');     //准许上传的文件后缀
		$upload->allowTypes= array('image/jpeg','image/pjpeg','image/png','image/gif','image/x-png');//准许上传的文件类型
		$upload->autoSub=false; //是否使用子目录进行保存上传文件
		if($upload->upload())
		{
			$info=$upload->getUploadFileInfo();
			$config = config('basic');
			if($config['watermark'] == 1)
			{
				Image::water($info[0]['savepath'].$info[0]['savename'], './Public/Uploads/logo/'.$config['watermarkimg']);
			}
			return $info;
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}


	private function uplink()
	{
		$upload=new UploadFile();
		$upload->maxSize='20480000';  
		$upload->savePath='./Public/Uploads/link/';
		$upload->saveRule= 'time';
		$upload->uploadReplace=true;     
		$upload->allowExts=array('jpg','jpeg','png','gif');     //准许上传的文件后缀
		$upload->allowTypes=array('image/jpeg','image/pjpeg','image/png','image/gif','image/x-png');//准许上传的文件类型
		$upload->autoSub=false; //是否使用子目录进行保存上传文件
		if($upload->upload())
		{
			$info=$upload->getUploadFileInfo();
			return $info;
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}


	private function upthumb()
	{
		$upload=new UploadFile();
		$upload->maxSize='20480000';  
		$upload->savePath='./Public/Uploads/thumb/';
		$upload->saveRule= 'time';
		$upload->uploadReplace=true;     
		$upload->allowExts=array('jpg','jpeg','png','gif');     //准许上传的文件后缀
		$upload->allowTypes=array('image/jpeg','image/pjpeg','image/png','image/gif','image/x-png');//准许上传的文件类型
		/*$upload->thumb=true;   //是否开启图片文件缩略,true表示开启
		$upload->thumbMaxWidth='500';  //以字串格式来传，如果你希望有多个，那就在此处，用,分格，写上多个最大宽
		$upload->thumbMaxHeight='400';	
		$upload->thumbPrefix='thumb_';//缩略图文件前缀
		$upload->thumbPath='./Public/Uploads/thumb/' ;
		$upload->thumbRemoveOrigin=1;*/	
		if($upload->upload())
		{
			$info=$upload->getUploadFileInfo();
			$config = config('basic');
			if($config['watermark'] == 1)
			{
			    //Image::water($info[0]['savepath'].'/thumb_'.$info[0]['savename'], $info[0]['savepath'].'/../logo/'.$config['watermarkimg']);
				Image::water($info[0]['savepath'].'/'.$info[0]['savename'], $info[0]['savepath'].'/../logo/'.$config['watermarkimg']);
			}
			return $info;
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}


	//上传附件方法
	private function upattach()
	{
		$upload=new UploadFile();
		$upload->maxSize='20480000';  
		$upload->savePath='./Public/Uploads/attach/';
		$upload->saveRule= 'time';
		$upload->uploadReplace = true; 
		$upload->allowExts = array('zip','rar','txt','ppt','pptx','cls','clsx','doc','docx','swf','jpg','png','gif','tar.gz','.7z');     //准许上传的文件后缀
		if($upload->upload())
		{
			$info=$upload->getUploadFileInfo();
			return $info;
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}

	//上传方法,提取公共代码
	private function upmethod($upload,$method)
	{
		if(empty($_FILES))
		{
			$this->error('必须选择上传文件');
			
		}
		$a=$this->$upload();
		if(isset($a))
		{
			if($this->$method($a))
			{
				$this->success('上传成功');
			}
			else
			{
				$this->error('插入文本框失败');
			}
		}
		else
		{
			$this->error('上传文件有异常请与系统管理员联系');
		}
		
	}
}
?>