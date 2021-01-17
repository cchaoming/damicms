<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 友情链接管理

    @Filename LinkAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:26:03 $
*************************************************************/
namespace app\admin\controller;
use until\Page;

class Link extends Common
{	
    public function index()
    {
		$link = M('link');
		$count = $link->count();
		$p = new Page($count,20);
		$p->setConfig('prev','上一页'); 
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$list = $link->orderRaw('`rank` asc')->limit($p->firstRow,$p->listRows)->select()->toArray();
		$this->assign('list',$list);
		return $this->display();
    }
	
	  public function add()
    {
		return $this->display('add');
    }
	
	 public function edit()
    {
        $id = (int)$this->request->param('id');
		$link = M('link');
		$list = $link->where('id='.$id)->find();
		$this->assign('list',$list);
		return $this->display();
    }
	
	public function doedit()
    {
		if($this->request->isPost()){
		$data['title'] = $_POST['title'];
		$data['url'] = $_POST['url'];
		$data['rank'] = $_POST['rank'];
		$data['logo'] = $_POST['logo'];
		$data['islogo'] = $_POST['islogo'];
		$link = M('link');
		if($link->whereRaw('id='.(int)$_POST['id'])->save($data))
		{
			$this->assign("jumpUrl",U('Link/index'));
			$this->success('操作成功!');
		}
        }
		$this->error('操作失败!');
    }
	public function doadd()
    {
		$data['title'] = $_POST['title'];
		$data['url'] = $_POST['url'];
		$data['rank'] = $_POST['rank'];
		$data['logo'] = $_POST['logo'];
		$data['islogo'] = $_POST['islogo'];
		$data['status'] = 1;
		$link = M('link');
		if($link->save($data))
		{
			$this->assign("jumpUrl",U('Link/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function del()
    {
        $id = (int)$this->request->param('id');
		$type = M('link');
		if($id)
		{
            $type->where('id='.$id)->delete();
			$this->assign("jumpUrl",U('Link/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');	
    }
	
	public function status(){
		$link = M('link');
        $id = (int)$this->request->param('id');
        $status = (int)$this->request->param('status');
		if($status == 0)
		{
			$link->where( 'id='.$id )->save(['status'=>1]);
		}
		elseif($status==1)
		{
			$link->where( 'id='.$id )->save(['status'=>0]);
		}
		else
		{
			$this->error('非法操作!');
		}
		return $this->redirect('index');
	}

	
	public function delall()
	{
        $id = $_REQUEST['id'];  //获取文章id
        $id = is_array($id) ? $id : explode(',',$id);
        if(!$id)
        {
            $this->error('请先勾选记录!');
        }
        $map[] = ['id','in',$id];
		$link = M('link');
		if($_REQUEST['Del'] == '编辑')
		{ 
			for($i = 0;$i < count($_REQUEST['linkid']);$i++)
			{
			    $data =[];
				$data['title'] = $_REQUEST['title'][$i];
				$data['url'] = $_REQUEST['url'][$i];
				$data['rank'] = $_REQUEST['rank'][$i];
				$link->removeOption()->whereRaw('id='.$_REQUEST['linkid'][$i])->save($data);
			}
			$this->assign("jumpUrl",U('Link/index'));
			$this->success('操作成功!');
		}

		if($_REQUEST['Del'] == '删除') 
		{ 
			if($link->where($map)->delete())
			{
				$this->assign("jumpUrl",U('Link/index'));
			$this->success('操作成功!');
			}
		}
		
		if($_REQUEST['Del'] == '批量显示') 
		{ 
			$data['status'] = 1;
			if($link->where($map)->save($data) !== false)
			{
			$this->assign("jumpUrl",U('Link/index'));
			$this->success('操作成功!');
				
			}
		}
		
		if($_REQUEST['Del'] == '批量隐藏')
		{ 
			$data['status']=0;
			if($link->where($map)->save($data) !== false)
			{
				$this->assign("jumpUrl",U('Link/index'));
			$this->success('操作成功!');
			}
		}
	}
}
?>