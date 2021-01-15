<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 文章管理

    @Filename ArticleAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-27 08:52:44 $
*************************************************************/
class UpdateAction extends CommonAction
{	
  function index(){
	  if(intval(I('get.doup')) == 1){
	  import('ORG.Util.Pclzip');
	  clearstatcache();
	  $url = "http://www.damicms.com/update/vip_update.zip";
	  $save_path = './Public/';
	  $ext_path ='./Public/uptmp/';
	  @mk_dir($ext_path);
	  $save_file = $save_path."uptemp.zip"; 
	  if(!file_exists($save_file) || filemtime($save_file)< (time()-6*3600)){	 
	  echo '准备下载升级文件包....<br>';	
	  ob_flush();flush();  
	  self::get_damipacket($url,$save_file);
	   }
	   clearstatcache();
	  if(file_exists($save_file)){	  
	  echo '正在解压升级文件包....<br>';
	  ob_flush();flush();
	  $archive = new PclZip($save_file);
        if ($archive->extract(PCLZIP_OPT_PATH, $ext_path) == 0) {
        die("Error : ".$archive->errorInfo(true));
        }else{		
		echo '解压缩已经完成,准备更新文件....<br>';
		ob_flush();flush();
		//遍历文件并更新
		self::up_file($ext_path);
		echo '恭喜您，所有文件升级完毕!';
		ob_end_flush();//清空浏览器缓存
		//删除升级文件
		@deldir($ext_path);
		}
	  }
	  }else{
		  $this->display();
	  }
  }
 //获取升级最新包
function get_damipacket($url,$fileName){	   
	  if(function_exists('curl_init')){
	    $ch = curl_init(); 
        $fp = fopen($fileName, 'wb'); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_FILE, $fp); 
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 600); 
        curl_exec($ch); 
        curl_close($ch); 
        fclose($fp); 
	  }else if(ini_get("allow_url_fopen") == "1"){
      $file = fopen ($url, "rb");
      $newf = fopen ($fileName, "wb");   
      while (!feof($file)) { 
      fwrite($newf, fread($file, 1024 * 8), 1024 * 8);  
      }  
	  }
}
 //更新文件
function up_file($dirname){
	$dir = opendir($dirname);
			while($filename = readdir($dir))
			{
				if($filename != "." && $filename != "..")
				{
					$file = $dirname."/".$filename;
					if(is_dir($file))
					{
						self::up_file($file); //使用递归删除子目录	
					}
					else
					{
						//比较文件时间
						$old_file = str_replace('/Public/uptmp/','',$file);
						if(filemtime($file)>filemtime($old_file) || !file_exists($old_file)){
						echo '正在覆盖文件'.$old_file.'....<br>';	
						ob_flush();flush();
						@copy($file,$old_file);	  
						}
					}
				}
			}
closedir($dir);
}
//结束 
}
?>