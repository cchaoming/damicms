<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 关键字管理

    @Filename KeyAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:20:24 $
*************************************************************/
class KeyAction extends CommonAction
{	
    public function index()
    {
		$key = M('key');
		$count = $key->count();
		import('ORG.Util.Page');
		$p = new Page($count,20);
		$p->setConfig('prev','上一页'); 
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$list = $key->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('list',$list);
		$this->display('index');	
    }
	
	public function add()
    {
        $this->display('add');
    }
	
	public function doadd()
    {
		$key = M('key');
		$key->create(); 
		if($key->add())
		{
			$this->assign("jumpUrl",U('Key/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function edit()
    {
		$key = M('key');
		$list = $key->where('id='.$_GET['id'])->find();
		$this->assign('list',$list);
        $this->display();
    }
	
	public function doedit()
    {
		$key = M('key');
		$key->create();
		if($key->save())
		{
			$this->assign("jumpUrl",U('Key/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function del()
    {
		$type = M('key');
		if($type->where('id='.$_GET['id'])->delete())
		{
			$this->assign("jumpUrl",U('Key/index'));
			$this->success('操作成功!');
			
		}
		$this->error('操作失败!');
		
    }
	
	public function delall()
	{
		$id = $_REQUEST['id'];  //获取id
		$ids = implode(',',$id);//批量获取id
		$id = is_array($id)?$ids:$id;
		$map['id'] = array('in',$id); 
		$key = M('key');
		if($_REQUEST['Del'] == '编辑')
		{ 
			for($i = 0;$i < count($_REQUEST['keyid']);$i++)
			{
				$data['url'] = $_REQUEST['url'][$i];
				$key->where('id='.$_REQUEST['keyid'][$i])->save($data);
			}
			$this->assign("jumpUrl",U('Key/index'));
			$this->success('操作成功!');
		}
		
		if(!$id)
		{
			$this->error('请勾选记录!');
		}
		
		if($_REQUEST['Del'] == '删除')
		{ 
			if($key->where($map)->delete())
			{
				$this->assign("jumpUrl",U('Key/index'));
			$this->success('操作成功!');
			}
		$this->error('操作失败!');
		}
	}
	public function search()
	{
		$key = M('key');
		$count = $key->count();
		import('ORG.Util.Page');
		$p = new Page($count,20);
		$p->setConfig('prev','上一页'); 
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$map['title'] = array('like','%'.$_POST['keywords'].'%'); 
		$list = $key->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('list',$list);
		$this->display('index');	
	}
}
?>