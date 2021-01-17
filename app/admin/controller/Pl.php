<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
	
    @function 评论管理

    @Filename PlAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-17 15:13:19 $
*************************************************************/
namespace app\admin\controller;
use until\Page;

class Pl extends Common
{	
    public function index()
    {
		$pl = M('pl');
		$where = '1=1';
		if($this->request->param('status'))
		{
		    $where.=' and status='.(int)$this->request->param('status');
			}
        $count = $pl->whereRaw($where)->orderRaw('ptime desc')->count();
        $p = new Page($count,20);
        $list = $pl->limit($p->firstRow,$p->listRows)->select()->toArray();
        $p->setConfig('prev','上一页');
		$p->setConfig('header','条评论');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条评论 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		return $this->display();
    }
	
	 public function edit()
    {
        $id = (int)$this->request->param('id');
		$type = M('pl');
		$list = $type->where('id='.$id)->find();
		$this->assign('list',$list);
		return $this->display();
    }
	
	public function doedit()
    {
        $id = (int)$this->request->param('id');
		$pl=M('pl');
	//使用stripslashes 反转义,防止服务器开启自动转义
		$data['content'] = stripslashes($_POST['content']);
		$data['recontent'] = stripslashes($_POST['recontent']);
		if($pl->whereRaw('id='.$id)->save($data))
		{
			$this->assign("jumpUrl",U('Pl/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function del()
    {
		$type = M('pl');
        $id = (int)$this->request->param('id');
		if($id)
		{
            $type->where('id='.$id)->delete();
			$this->assign("jumpUrl",U('Pl/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');
    }
	
	public function status(){
		$pl = M('pl');
        $id = (int)$this->request->param('id');
        $status = (int)$this->request->param('status');
		if($status == 0)
		{
			$pl->whereRaw( 'id='.$id )->save(['status'=>1]);
		}
		elseif($status == 1)
		{
			$pl->whereRaw( 'id='.$id )->save(['status'=>0]);
		}else{
			$this->error('非法操作!');
		}
		return $this->redirect('index');
	}


	public function delall(){
        $id = $_REQUEST['id'];  //获取文章id
        $id = is_array($id) ? $id : explode(',', $id);
        if (!$id) {
            $this->error('请先勾选记录!');
        }
        $map[] = ['id', 'in', $id];
		$pl = M('pl');
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
			if($pl->where($map)->save($data) !== false)
			{
				$this->assign("jumpUrl",U('Pl/index'));
			$this->success('操作成功!');
			}
			$this->error('操作失败!');
		}
		
		if($_REQUEST['Del'] == '批量审核')
		{
			$data['status'] = 1;
			if($pl->where($map)->save($data) !== false)
			{
				$this->assign("jumpUrl",U('Pl/index'));
			$this->success('操作成功!');
			}
				$this->error('操作失败!');
		}
	}

	public function search()
	{
	    $keyword = (string)$this->request->param('keywords');
	    if(!$keyword){$this->error('操作失败!');}
		$pl = M('pl');
		$map[] = ['content','like','%'.$keyword.'%'];
		$count = $pl->where($map)->orderRaw('ptime desc')->count();
		$p = new Page($count,20);
		$list = $pl->limit($p->firstRow,$p->listRows)->select()->toArray();
		if($this->request->isPost()){
            $p->parameter .= 'keywords='.$keyword;
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
		return $this->display('index');
	}
}
?>