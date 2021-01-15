<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 标签管理

    @Filename LabelAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:25:56 $
*************************************************************/
class LabelAction extends CommonAction
{
    public function index()
    {
		$label = M('label');
		$list = $label->order('addtime desc')->select();
		$this->assign('list',$list);
		$this->display('index');
    }
//添加标签
	public function add()
    {
        $this->display('add');
    }
//执行添加
	public function doadd()
    {
		$label = M('label');
		$data['title'] = $_POST['title'];
	//使用stripslashes 反转义,防止服务器开启自动转义
		$data['content'] = stripslashes($_POST['content']);
		$data['addtime'] = date('Y-m-d H:i:s');
		if($label->add($data))
		{
			$this->assign("jumpUrl",U('Label/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }

	public function edit()
    {
		$label = M('label');
		$list = $label->where('id='.$_GET['id'])->find();
		$this->assign('list',$list);
        $this->display();
    }
	
	public function doedit()
    {
		$label = M('label');
		$data['id'] = $_POST['id'];
		$data['title'] = $_POST['title'];
		$data['content'] = stripslashes($_POST['content']);
		$data['addtime'] = date('Y-m-d H:i:s');
		if($label->save($data))
		{
			$this->assign("jumpUrl",U('Label/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function del()
    {
		$type = M('label');
		if($type->where('id='.$_GET['id'])->delete())
		{
			$this->assign("jumpUrl",U('Label/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }

		public function status()
	{
		$status=M('label');
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