<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 万能表格模型管理

    @Filename LinkAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:26:03 $
*************************************************************/
namespace app\admin\controller;
use until\Page;

class Adminlog extends Common
{	
    private $model;
    public  function initialize() {
	parent::initialize();
	$this->model = D("log");
	}
    public function index()
    {
		$where = '1=1';
		if ($this->request->param('username')) {
			$where .= " and username='".urldecode($this->request->param('username'))."'";
		}
		if ($this->request->param('keyword')) {
			$where .= " and (operate like'%".urldecode($this->request->param('keyword'))."%' or result like '%".urldecode($this->request->param('keyword'))."%')";
		}
		if ($this->request->param('start_time')) {
			$where .= " and addtime>=".strtotime($this->request->param('start_time'));
		}
		if ($this->request->param('end_time')) {
			$where .= " and addtime<=".strtotime($this->request->param('end_time'));
		}

		$count = $this->model->whereRaw($where)->count();
		//echo $this->model->getLastSql();
		$p = new Page($count,20);//分页条数
		if ($_POST) {
			$allow_par = array('p','keyword','username','start_time','end_time');
			foreach ($_POST as $key => $val) {
				if(in_array($key,$allow_par)){
					if($_POST[$key] != '') {
						$p->parameter .= "/$key/" . urlencode($val);
					}
				}
			}
		}
		$p->setConfig('prev','上一页'); 
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$list = $this->model->newInstance()->whereRaw($where)->orderRaw('addtime desc')->limit($p->firstRow,$p->listRows)->select()->toArray();
		$this->assign('list',$list);
		return $this->display();
    }
	
	public function add()
    {
		return $this->display('add');
    }
	/*public function doadd()
    {
	$data = $_POST;
	$this->model->add($data);
	$this->success('添加成功!');	
    }*/
	public function edit(){
	$id = (int)$this->request->param('id');
	$info = $this->model->whereRaw('id='.$id)->find();
	$this->assign('info',$info);
	return $this->display('add');
	}
	/*public function doedit(){
	$data = $_POST;
	$this->model->whereRaw('id='.$_REQUEST['id'])->save($data);
	$this->success('修改成功!');	
	}
	public function del()
    {
		if($this->model->whereRaw('id='.$_REQUEST['id'])->delete())
		{
			$this->success('操作成功!');
		}
		$this->error('操作失败!');	
    }	*/
}
?>