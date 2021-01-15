<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
	
    @function 评论管理

    @Filename PlAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-17 15:13:19 $
*************************************************************/
class PlAction extends CommonAction
{	
    public function index()
    {
		import('ORG.Util.Page');
		R("Article/urlmode");
		$pl = M('pl');
		if(isset($_GET['status']))
		{
			$count = $pl->where('status='.$_GET['status'])->order('ptime desc')->count();
			$p = new Page($count,20);
			$list = $pl->where('status='.$_GET['status'])->order('ptime desc')->limit($p->firstRow.','.$p->listRows)->select();
		}
		else
		{
			$count = $pl->order('ptime desc')->count();
			$p = new Page($count,20); 
			$list = $pl->order('ptime desc')->limit($p->firstRow.','.$p->listRows)->select();
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
		$this->display();
    }
	
	 public function edit()
    {
		$type = M('pl');
		$list = $type->where('id='.$_GET['id'])->find();
		$this->assign('list',$list);
		$this->display();
    }
	
	public function doedit()
    {
		$pl=M('pl');
		$data['id'] = $_POST['id'];
	//使用stripslashes 反转义,防止服务器开启自动转义
		$data['content'] = stripslashes($_POST['content']);
		$data['recontent'] = stripslashes($_POST['recontent']);
		if($pl->save($data))
		{
			$this->assign("jumpUrl",U('Pl/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function del()
    {
		$type = M('pl');
		if($type->where('id='.$_GET['id'])->delete())
		{
			$this->assign("jumpUrl",U('Pl/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function status(){
		$pl = M('pl');
		if($_GET['status'] == 0)
		{
			$pl->where( 'id='.$_GET['id'] )-> setField( 'status',1);
		}
		elseif($_GET['status'] == 1)
		{
			$pl->where( 'id='.$_GET['id'] )-> setField( 'status',0);
		}else{
			$this->error('非法操作!');
		}
		$this->redirect('index');
	}


	public function delall(){
		$id = $_REQUEST['id'];  //获取文章id
		$ids = implode(',',$id);//批量获取id
		$id = is_array($id) ? $ids : $id;
		$map['id'] = array('in',$id);
		if(!$id)
		{
			$this->error('请先勾选记录!');
		}
		$pl = M(pl);
		if($_REQUEST['Del'] == '删除')
		{
			if($pl->where($map)->delete())
			{
				$this->assign("jumpUrl",U('Pl/index'));
			$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
		
		if($_REQUEST['Del'] == '批量未审')
		{
			$data['status'] = 0;
			if($pl->where($map)->save($data))
			{
				$this->assign("jumpUrl",U('Pl/index'));
			$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
		
		if($_REQUEST['Del'] == '批量审核')
		{
			$data['status'] = 1;
			if($pl->where($map)->save($data))
			{
				$this->assign("jumpUrl",U('Pl/index'));
			$this->success('操作成功!');
			}
				$this->error('操作失败!');
		}
	}

	public function search()
	{
		import('ORG.Util.Page');
		R("Article/urlmode");
		$pl = M('pl');
		$map['content'] = array('like','%'.$_POST['keywords'].'%');
		$count = $pl->where($map)->order('ptime desc')->count();
		$p = new Page($count,20); 
		$list = $pl->where($map)->order('ptime desc')->limit($p->firstRow.','.$p->listRows)->select();
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