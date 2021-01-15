<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
	
    @function:投票管理模块

    @Filename VoteAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:33:32 $
*************************************************************/
class VoteAction extends CommonAction
{	
     public function index()
    {
		import('ORG.Util.Page');
		$vote = M('vote');
		$count = $vote->count();
		$p = new Page($count,20); 
		$p->setConfig('prev','上一页'); 
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$list = $vote->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('list',$list);
		$this->display();	
    }
	
	public function add()
    {
        $this->display();
    }
	
	public function doadd()
    {
		$vote=M('vote');
		$vote->create(); 
		if($vote->add())
		{
			$this->assign("jumpUrl",U('Vote/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');	
    }

	   public function edit()
    {
		$vote = M('vote');
		$list = $vote->where('id='.$_GET['id'])->find();
		$this->assign('list',$list);
        $this->display();
    }
	
	public function doedit()
    {
		$data['id'] = $_POST['id'];
		$data['vote'] = $_POST['vote'];
		$data['title'] = $_POST['title'];
		$data['starttime'] = $_POST['starttime'];
		$data['overtime'] = $_POST['overtime'];
		$data['rank'] = $_POST['rank'];
		$data['stype'] = $_POST['stype'];
		$vote = M('vote');
		if($vote->save($data))
		{
			$this->assign("jumpUrl",U('Vote/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function del()
    {
		$type = M('vote');
		if($type->where('id='.$_GET['id'])->delete())
		{
			$this->assign("jumpUrl",U('Vote/index'));
			$this->success('操作成功!');	
		}
		$this->error('操作失败!');
    }
	
	public function status()
	{
		$a = M('vote');
		if($_GET['status'] == 0)
		{
			$a->where('id='.$_GET['id'])->setField('status',1); 
		}
		elseif($_GET['status']==1)
		{
			$a->where('id='.$_GET['id'])-> setField('status',0); 
		}
		else
		{
			$this->error("非法操作!");
		}
		$this->redirect('index');
	}
	
	public function delall()
	{
		$id = $_REQUEST['id'];  //获取id
		$ids = implode(',',$id);//批量获取id
		$id = is_array($id) ? $ids : $id;
		$map['id'] = array('in',$id); 
		if(!$id)
		{
			$this->assign("jumpUrl",U('Vote/index'));
			$this->error('请勾选记录!');
		}
		
		$vote = M('vote');
		
		if($_REQUEST['Del'] == '删除') 
		{ 
			if($vote->where($map)->delete())
			{
				$this->assign("jumpUrl",U('Vote/index'));
			$this->success('操作成功!');
			}
			$this->error('删除数据失败!');
		}
		
		if($_REQUEST['Del'] == '隐藏')
		{
			$data['status'] = 0;
			if($vote->where($map)->save($data))
			{
				$this->assign("jumpUrl",U('Vote/index'));
			$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
		
		if($_REQUEST['Del']=='显示')
		{
			$data['status'] = 1;		
			if($vote->where($map)->save($data))
			{
				$this->assign("jumpUrl",U('Vote/index'));
			$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
	}
	
	public function show()
	{
		$vote = M('vote');
		$vo = $vote->where('id='.$_GET['id'])->find();
		$strs = explode("\n",trim($vo['vote']));
		$total = 0;
		
		for($i = 0;$i < count($strs);$i++)
		{
			$s = explode("=",$strs[$i]);
			$data[$i]['num'] = $s[1];
			$data[$i]['title'] = $s[0];
			$total += $s[1];
		}
		
		foreach($data as $k=>$v)
		{
			$data[$k]['percent'] = round($v['num'] / $total * 100 + 0);
		}
		$this->assign('list',$data);
		$this->display();
	}
}
?>