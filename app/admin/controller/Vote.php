<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
	
    @function:投票管理模块

    @Filename VoteAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:33:32 $
*************************************************************/
namespace app\admin\controller;
use until\Page;

class Vote extends Common
{	
     public function index()
    {
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
		$list = $vote->limit($p->firstRow,$p->listRows)->select()->toArray();
		$this->assign('list',$list);
		return $this->display();
    }
	
	public function add()
    {
        return $this->display();
    }
	
	public function doadd()
    {
        if($this->request->isPost()){
		$vote=M('vote',true);
		if($vote->save($_POST))
		{
			$this->assign("jumpUrl",U('Vote/index'));
			$this->success('操作成功!');
		}
        }
		$this->error('操作失败!');	
    }

	   public function edit()
    {
        $id = (int)$this->request->param('id');
		$vote = M('vote');
		$list = $vote->whereRaw('id='.$id)->find();
		$this->assign('list',$list);
        return $this->display();
    }
	
	public function doedit()
    {
        if($this->request->isPost()){
		$data['vote'] = $_POST['vote'];
		$data['title'] = $_POST['title'];
		$data['starttime'] = $_POST['starttime'];
		$data['overtime'] = $_POST['overtime'];
		$data['rank'] = $_POST['rank'];
		$data['stype'] = $_POST['stype'];
		$vote = M('vote',true);
		$info = $vote->find(intval($_POST['id']));
		if($info && $info->save($data))
		{
			$this->assign("jumpUrl",U('Vote/index'));
			$this->success('操作成功!');
		}
        }
		$this->error('操作失败!');
    }
	
	public function del()
    {
        $id = (int)$this->request->param('id');
		$type = M('vote');
		if($id)
		{
            $type->whereRaw('id='.$id)->delete();
			$this->assign("jumpUrl",U('Vote/index'));
			$this->success('操作成功!');	
		}
		$this->error('操作失败!');
    }
	
	public function status()
	{
		$a = M('vote');
        $id = (int)$this->request->param('id');
        $status = (int)$this->request->param('status');
        if ($status == 0) {
            $a->whereRaw('id=' . $id)->save(['status'=>1]);
        } else {
            $a->whereRaw('id=' . $id)->save(['status'=>0]);
        }
		return $this->redirect('Vote/index');
	}
	
	public function delall()
	{
		$id = $_REQUEST['id'];  //获取id
		$id = is_array($id) ? $id : explode(',',$id);
		if(!$id)
		{
			$this->assign("jumpUrl",U('Vote/index'));
			$this->error('请勾选记录!');
		}
        $map[] = ['id','in',$id];
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
			if($vote->where($map)->save($data) !== false)
			{
				$this->assign("jumpUrl",U('Vote/index'));
			$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
		
		if($_REQUEST['Del']=='显示')
		{
			$data['status'] = 1;		
			if($vote->where($map)->save($data) !== false)
			{
				$this->assign("jumpUrl",U('Vote/index'));
			$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
	}
	
	public function show()
	{
        $id = (int)$this->request->param('id');
		$vote = M('vote');
		$vo = $vote->whereRaw('id='.$id)->find();
		$strs = explode("\n",trim($vo['vote']));
		$total = 0;
		
		for($i = 0;$i < count($strs);$i++)
		{
			$s = explode("=",$strs[$i]);
			$data[$i]['num'] = (int)$s[1];
			$data[$i]['title'] = $s[0];
			$total += (int)$s[1];
		}
		
		foreach($data as $k=>$v)
		{
			$data[$k]['percent'] = round($v['num'] / $total * 100 + 0);
		}
		$this->assign('list',$data);
		return $this->display();
	}
}
?>