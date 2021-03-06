<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 模板管理

    @Filename TplAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-12-12 20:10:23 $
*************************************************************/
namespace app\admin\controller;
use until\Dir;

class Tpl extends Common
{
		public function index()
		{
			$dirpath = $this->dirpath();//当前目录
			$dirlast = $this->dirlast();//上一层目录
			$dir = new Dir($dirpath);
			$list_dir = $dir->toArray();
			if (empty($list_dir))
			{
				$this->error('该文件夹下面没有文件！');
			}
			foreach($list_dir as $key=>$value)
			{
				$list_dir[$key]['pathfile'] = dami_url_repalce($value['path'],'desc').'|'.$value['filename'];
			}
			session('tpl_jumpurl', urlencode(url('Tpl/index',['id'=>dami_url_repalce($dirpath,'desc')])));
			if($dirlast && $dirlast != '.')
			{
				$this->assign('dirlast',dami_url_repalce($dirlast,'desc'));
			}
			$this->assign('dirpath',$dirpath);
			$this->assign('list_dir',list_sort_by($list_dir,'mtime','desc'));
			return $this->display('index');
		}
	//获取模板当前路径
	public function dirpath()
	{
		$id = dami_url_repalce(trim($this->request->param('id')));
		if ($id) 
		{
			$dirpath = $id;
		}
		else
		{
			$dirpath ='./template';
		}
		if (strpos($dirpath,'template') === false)
		{
			$this->error("不在模板文件夹范围内！");
		}
		return $dirpath;
	}
	//获取模板上一层路径
	public function dirlast()
	{
		$id = dami_url_repalce(trim($this->request->param('id')));
		if ($id) 
		{
			return substr($id,0,strrpos($id, '/'));
		}
		else
		{
			return false;
		}
	}
	// 编辑模板
	public function add()
	{
		$filename = dami_url_repalce(str_replace('*','.',trim($this->request->param('id'))));
		if (empty($filename)) 
		{
			$this->error('模板名称不能为空！');
		}
		$content = read_file($filename);
		$this->assign('filename',$filename);
		$this->assign('content',htmlspecialchars($content));
		return $this->display('add');
	}
	// 更新模板
	public function update()
	{
		$filename = trim($_POST['filename']);
		$ext = strtolower(substr($filename, strrpos($filename, '.')+1));
		$allow = ['html','htm','txt','css','js','tpl'];
		if(!in_array($ext,$allow)){alert('该文件不能编辑!');}
		$content = stripslashes(htmlspecialchars_decode($_POST['content']));
		if (!testwrite(substr($filename,0,strrpos($filename,'/'))))
		{
			$this->error('在线编辑模板需要给/template添加写入权限！');
		}
		if (empty($filename))
		{
			$this->error('模板文件名不能为空！');
		}
		if (empty($content))
		{
			$this->error('模板内容不能为空！');
		}
		write_file($filename,$content);
		if (session('tpl_jumpurl'))
		{
			$this->assign("jumpUrl",urldecode(session('tpl_jumpurl')));
		}
		else
		{
			$this->assign("jumpUrl",U('Tpl/index'));
		}
		$this->success('恭喜您，模板更新成功！');
	}
	// 删除模板
    public function del()
	{
		$id = dami_url_repalce(str_replace('*','.',trim($_GET['id'])));
		if (!substr(sprintf("%o",fileperms($id)),-3))
		{
			$this->error('无删除权限！');
		}
		@unlink($id);
        if (session('tpl_jumpurl'))
        {
            $this->assign("jumpUrl",urldecode(session('tpl_jumpurl')));
        }
        else
        {
            $this->assign("jumpUrl",U('Tpl/index'));
        }
		$this->success('删除文件成功！');
    }
	
	
}
?>