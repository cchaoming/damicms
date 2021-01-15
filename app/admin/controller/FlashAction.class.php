<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 幻灯管理

    @Filename FlashAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:11:14 $
*************************************************************/
 class FlashAction extends CommonAction
 {	
    public function index()
	{
		$flash = M('flash');
		$list = $flash->select();
		$this->assign('list',$list);
		$this->display('index');
	}
	
	public function add()
	{
		$this->display('add');
	}
	
	public function doadd()
	{
		//dump($_POST);
		$data['title'] = $_POST['title']; 
		$data['pic'] = $_POST['pic']; 
		$data['url'] = $_POST['url']; 
		$data['status'] = $_POST['status']; 
		$data['rank'] = $_POST['rank']; 
		$flash = M('flash');
		if($flash->add($data))
		{
			$this->assign("jumpUrl",U('Flash/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
	}
	
	public function edit()
	{
		$flash = M('flash');
		$list = $flash->where('id='.$_GET['id'])->find();
		$this->assign('list',$list);
		$this->display('edit');
	}
	
	public function doedit()
	{
		$data['title'] = $_POST['title']; 
		$data['pic'] = $_POST['pic']; 
		$data['url'] = $_POST['url']; 
		$data['status'] = $_POST['status']; 
		$data['rank'] = $_POST['rank']; 
		$data['id'] = $_POST['id']; 
		$flash = M('flash');
		if($flash->save($data))
		{
		    $this->assign("jumpUrl",U('Flash/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
	}
	
	public function del()
    {
		$flash = M('flash');
		if($flash->where('id='.$_GET['id'])->delete())
		{
			$this->assign("jumpUrl",U('Flash/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function status()
	{
		$status=M('flash');
		if($_GET['status'] == 0)
		{
			$status->where( 'id='.$_GET['id'] )-> setField( 'status',1); 
		}
		elseif($_GET['status'] == 1)
		{
			$status->where( 'id='.$_GET['id'] )-> setField( 'status',0); 
		}
		else
		{
			$this->error('非法操作!');
		}
		$this->redirect('index');
	}
}

?>