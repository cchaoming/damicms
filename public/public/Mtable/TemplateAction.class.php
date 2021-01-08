<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 万能表格模型管理

    @Filename LinkAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:26:03 $
*************************************************************/
class TemplateAction extends CommonAction
{	
    private $model;
    function _initialize() {
	parent::_initialize();
	$this->model = new Model("table_name",null);
	}
    public function index()
    {
		$where = '1=1';
		$count = $this->model->where($where)->count();
		import('ORG.Util.Page');
		$p = new Page($count,20);//分页条数
		$p->setConfig('prev','上一页'); 
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$list = $this->model->where($where)->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('list',$list);
		$this->display();
    }
	
	public function add()
    {
		$this->display('add');
    }
	public function doadd()
    {
	$data = $_POST;
	$this->model->add($data);
	$this->success('添加成功!');	
    }
	public function edit(){	
	$info = $this->model->where('zj_id='.$_REQUEST['zj_id'])->find();
	$this->assign('info',$info);
	$this->display('add');	
	}
	public function doedit(){
	$data = $_POST;
	$this->model->where('zj_id='.$_REQUEST['zj_id'])->save($data);
	$this->success('修改成功!');	
	}
	public function del()
    {
		if($this->model->where('zj_id='.$_REQUEST['zj_id'])->delete())
		{
			$this->success('操作成功!');
		}
		$this->error('操作失败!');	
    }	
}
?>