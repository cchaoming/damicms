<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 广告管理

    @Filename AdAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 09:59:26 $
*************************************************************/
class AdAction extends CommonAction
{	
    public function index()
    {

		$ad = M('ad');
		$count = $ad->count();
		import('ORG.Util.Page');
		isset($_GET['type']) ? $map['type'] = $_GET['type'] : $map['type'] = array('neq',10);
		isset($_GET['status']) ? $map['status'] = $_GET['status'] :$map['status'] = array('neq',2);
		$count = $ad->where($map)->count();
		$p = new Page($count,20);
		$p->setConfig('prev','上一页'); 
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$list=$ad->where($map)->order('addtime desc')->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('list',$list);
		$this->display();	
    }
	
	public function add()
    {
		$this->display('add');
    }
	
	public function edit()
    {
		$type = M('ad');
		$list = $type->where('id='.$_GET['id'])->find();
		$this->assign('list',$list);
		$this->display('edit');
    }
	
	public function doedit()
    {
		$ad = M('ad');
		$data['id'] = $_POST['id'];
		$data['title'] = $_POST['title'];
		$data['type'] = $_POST['type'];
		$data['description'] = $_POST['description'];
		//使用stripslashes 反转义,防止服务器开启自动转义
		$data['content'] = stripslashes($_POST['content']);
		$data['addtime'] = date('Y-m-d H:i:s');
		if($ad->save($data))
		{
			$this->assign("jumpUrl",U('Ad/index'));
			$this->success('操作成功!');
		}
       $this->error('操作失败!');
    }
	
	public function doadd()
    {
		$ad = M('ad');
		$data['title'] = $_POST['title'];
		$data['type'] = $_POST['type'];
		$data['description'] = $_POST['description'];
		$data['content'] = stripslashes($_POST['content']);
		$data['addtime'] = date('Y-m-d H:i:s');
		if($ad->add($data))
		{
			$this->assign("jumpUrl",U('Ad/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function del()
    {
		$type = M('ad');
		if($type->where('id='.$_GET['id'])->delete())
		{
			$this->assign("jumpUrl",U('Ad/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');	
    }
	
	public function status(){
		$a = M('ad');
		if($_GET['status'] == 0)
		{
			$a->where( 'id='.$_GET['id'] )-> setField( 'status',1); 
		}
		else
		{
			$a->where( 'id='.$_GET['id'] )-> setField( 'status',0); 
		}
		$this->redirect('index');
	}

	
	public function delall()
	{
		$id = $_REQUEST['id'];  //获取文章id
		$ids = implode(',',$id);//批量获取id
		$id = is_array($id) ? $ids : $id;
		$map['id'] = array('in',$id); 
		if(!$id)
		{
			$this->error('请勾选广告!');
		}
		$ad = M('ad');
		if($_REQUEST['Del'] == '显示')
		{ 
			$data['status'] = 1;
			if($ad->where($map)->save($data))
			{
			$this->assign("jumpUrl",U('Ad/index'));
			$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
		
		if($_REQUEST['Del'] == '隐藏')
		{ 
			$data['status'] = 0;
			if($ad->where($map)->save($data))
			{
			$this->assign("jumpUrl",U('Ad/index'));
			$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
	}
}
?>