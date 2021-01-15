<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
	
    @function:数据库备份与还原

    @Filename BackupAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-30 15:13:34 $
*************************************************************/
class BackupAction extends CommonAction
{	
     public function index()
    {
		$rs = new Model();
		$list = $rs->query("SHOW TABLES FROM "."`".C('DB_NAME')."`");
		$table = array();
        foreach ($list as $k => $v)
		{
            $table[$k] = current($v);
        }
		$this->assign('tablelist',$table);
		$this->display();
	}
	public function dobackup()
	{
		if(empty($_POST['ids']))
		{
			$this->error('请选择需要备份的数据库表！');
		}
		$filesize = intval($_POST['filesize']);
		if ($filesize < 512) 
		{
			$this->error('出错了,请为分卷大小设置一个大于512的整数值！');
		}
		$file ='./Public/Backup/';
		$random = mt_rand(10000000, 99999999);
		$sql = ''; 
		$p = 1;
		foreach($_POST['ids'] as $table)
		{
			$rs = D(str_replace(C('db_prefix'),'',$table));
			$array = $rs->select();
			$sql.= "TRUNCATE TABLE `$table`;\n";
			foreach($array as $value)
			{
				$sql.= $this->insertsql($table, $value);
				if (strlen($sql) >= $filesize*1000) 
				{
					$filename = $file.$random.'_'.date('YmdHis').'_'.$p.'.sql';
					write_file($filename,$sql);
					$p++;
					$sql='';
				}
			}
		}
		if(!empty($sql))
		{
			$filename = $file.$random.'_'.date('YmdHis').'_'.$p.'.sql';
			write_file($filename,$sql);
		}
		$this->assign("jumpUrl",U("Backup/restore"));
		$this->success('数据库分卷备份已完成,共分成'.$p.'个sql文件存放！');
	}
	//生成SQL备份语句
	public function insertsql($table, $row)
	{
		$sql = "INSERT INTO `{$table}` VALUES ("; 
		$values = array(); 
		foreach ($row as $value) 
		{
			$values[] = "'" . DbMysqli::getInstance()->escapeString($value) . "'"; 
		}
		$sql .= implode(', ', $values) . ");\n"; 
		return $sql;
	}
	//展示还原
    public function restore()
	{
		$filepath = './Public/Backup/*.sql';
		$filearr = glob($filepath);
		if (!empty($filearr)) 
		{
			foreach($filearr as $k=>$sqlfile)
				{
					preg_match("/([0-9]+)_([0-9]+)_([0-9]+)\.sql/i",basename($sqlfile),$num);
					$restore[$k]['filename'] = basename($sqlfile);
					$restore[$k]['filesize'] = round(filesize($sqlfile)/(1024*1024), 2);
					$restore[$k]['maketime'] = date('Y-m-d H:i:s', filemtime($sqlfile));
					$restore[$k]['rd'] = $num[1];
					$restore[$k]['pre'] = $num[2];
					$restore[$k]['number'] = $num[3];
					$restore[$k]['path'] = './Public/Backup/';
				}
			$this->assign('list',$restore);
        	$this->display('restore');
		}
		else
		{
			$this->assign("jumpUrl",U("Backup/index"));
			$this->error('没有检测到备份文件,请先备份或上传备份文件到./Public/Backup/');
		}
    }
	//导入还原
	public function back()
	{
		$rs = new Model();
		$pre = $_GET['id'];
		$rd = $_GET['rd'];
		$fileid = $_GET['fileid'] ? intval($_GET['fileid']) : 1;
		$filename = $rd.'_'.$pre.'_'.$fileid.'.sql';
		$filepath = './Public/Backup/'.$filename;
		if(file_exists($filepath))
		{
			$sql = read_file($filepath);
			$sql = str_replace("\r\n", "\n", $sql); 
			foreach(explode(";\n", trim($sql)) as $query)
			{
				$rs->execute(trim($query));
			}
			$this->assign("jumpUrl",U('Backup/back?rd='.$rd.'&id='.$pre.'&fileid='.($fileid+1)));
			$this->success('第'.$fileid.'个备份文件恢复成功,准备恢复下一个,请稍等！');
		}
		else
		{
			$this->assign("jumpUrl",U("Backup/index"));
			$this->success("数据库恢复成功！");
		}
		
	}
	//下载还原
	public function down()
	{
		
		
		$filepath = './Public/Backup/'.$_GET['id'];
		if (file_exists($filepath))
		{
			$pathinfo = pathinfo($filepath);
			if($pathinfo['extension'] == 'sql'){
			$filename = $filename ? $filename : basename($filepath);
			$filetype = trim(substr(strrchr($filename, '.'), 1));
			$filesize = filesize($filepath);
			header('Cache-control: max-age=31536000');
			header('Expires: '.gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
			header('Content-Encoding: none');
			header('Content-Length: '.$filesize);
			header('Content-Disposition: attachment; filename='.$filename);
			header('Content-Type: '.$filetype);
			readfile($filepath);
			exit;
			}
			
		}
		else
		{
			$this->error('出错了,没有找到分卷文件！');
		}
	}
	//删除分卷文件
	public function del()
	{
		$filename = trim($_GET['id']);
		$filepath = './Public/Backup/'.$filename;
		$pathinfo = pathinfo($filepath);
		if($pathinfo['extension'] == 'sql'){
		@unlink($filepath);
		}
		$this->assign("jumpUrl",U("Backup/restore"));
		$this->success($filename.'已经删除！');
	}
	//删除所有分卷文件
	public function delall()
	{
		if(empty($_POST['ids']))
		{
			$this->error("请先选择备份文件!");
		}
		foreach($_POST['ids'] as $value)
		{
		$filepath = './Public/Backup/'.$value;
		$pathinfo = pathinfo($filepath);
		if($pathinfo['extension'] == 'sql'){
		@unlink('./Public/Backup/'.$value);
		}
		}
		$this->assign("jumpUrl",U("Backup/restore"));
		$this->success('批量删除分卷文件成功！');
	}
	//上传备份文件
	public function upload()
	{
		$this->display('upload');
	}
	//执行上传
	public function doupload()
	{
		//处理文件名,获取原始文件名
		$filename = str_replace(".sql","",$_FILES['url']['name']);
		import('ORG.Util.UploadFile');
		$upload=new UploadFile();
		$upload->maxSize='2048000';  
		$upload->savePath='./Public/Backup/';
		$upload->saveRule= $filename;
		$upload->uploadReplace = true; 
		$upload->allowExts = array('sql');     //准许上传的文件后缀
		if($upload->upload())
		{
			$this->assign("jumpUrl",U("Backup/restore"));
			$this->success('上传成功!');
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}
}
?>