<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 留言管理

    @Filename GuestbookAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-17 15:03:17 $
*************************************************************/
class GuestbookAction extends CommonAction
{	
    public function index()
    {
		import('ORG.Util.Page');
		$guestbook = M('guestbook');
		if(isset($_GET['status']))
		{
			$count = $guestbook->where('status='.$_GET['status'])->order('addtime desc')->count();
			$p = new Page($count,20); 
			$list = $guestbook->where('status='.$_GET['status'])->order('addtime desc')->limit($p->firstRow.','.$p->listRows)->select();
		}
		else
		{
			$count = $guestbook->order('addtime desc')->count();
			$p = new Page($count,20); 
			$list = $guestbook->order('addtime desc')->limit($p->firstRow.','.$p->listRows)->select();
		}
		
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条评论');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条评论 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		$this->display('index');
    }
	
	public function edit()
    {
    $type = M('guestbook');
	$list = $type->where('id='.$_GET['id'])->find();
	$this->assign('list',$list);
    $this->display('edit');
    }
	
	public function doedit()
    {
		$guestbook = M('guestbook');
		$data['id'] = $_POST['id'];
	//使用stripslashes 反转义,防止服务器开启自动转义
		$data['content'] = stripslashes($_POST['content']);
		$data['recontent'] = stripslashes($_POST['recontent']);
		if($guestbook->save($data))
		{
			$this->assign("jumpUrl",U('Guestbook/index'));
$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function del()
    {
		$type = M('guestbook');
		if($type->where('id='.$_GET['id'])->delete())
		{
			$this->assign("jumpUrl",U('Guestbook/index'));
			$this->success('操作成功!');
		} 
		$this->error('操作失败!');
    }
	
	public function status()
	{
		$a = M('guestbook');
		if($_GET['status'] == 0)
		{
			$a->where( 'id='.$_GET['id'] )-> setField( 'status',1);
		}
		elseif($_GET['status'] == 1)
		{
			$a->where( 'id='.$_GET['id'] )-> setField( 'status',0);
		}
		else
		{
			$this->error('非法操作!');
		}
		$this->redirect('index');
	}

	public function delall()
	{
		$id = $_REQUEST['id'];  //获取文章id
		$ids = implode(',',$id);//批量获取id
		$id = is_array($id)?$ids:$id;
		$map['id'] = array('in',$id);
		if(!$id)
		{
			$this->error('请勾选记录!');
		}
		$guestbook = M('guestbook');
		if($_REQUEST['Del']	==	'删除')
		{
			if($guestbook->where($map)->delete())
			{
				$this->assign("jumpUrl",U('Guestbook/index'));
$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
		
		if($_REQUEST['Del']	==	'批量未审')
		{
			$data['status'] = 0;
			if($guestbook->where($map)->save($data))
			{
				$this->assign("jumpUrl",U('Guestbook/index'));
$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
		
		if($_REQUEST['Del']	==	'批量审核')
		{
			$data['status']=1;
			if($guestbook->where($map)->save($data))
			{
				$this->assign("jumpUrl",U('Guestbook/index'));
$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
	}

	public function search()
	{
		import('ORG.Util.Page');
		$guestbook = M('guestbook');
		$map['content'] = array('like','%'.$_POST['keywords'].'%');
		$count = $guestbook->where($map)->order('addtime desc')->count();
		$p = new Page($count,20); 
		$list = $guestbook->where($map)->order('addtime desc')->limit($p->firstRow.','.$p->listRows)->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条评论');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条评论 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		$this->display('index');
	}
}
?>