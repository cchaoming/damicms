<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 万能表格模型管理

    @Filename LinkAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:26:03 $
*************************************************************/
class MtableAction extends CommonAction
{	
    public function index()
    {
		$link = M('extend_menu');
		$where = "typeid=0 and action_name<>''";
		$count = $link->where($where)->count();
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
		$list = $link->where($where)->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('list',$list);
		$this->display();
    }
	
	  public function add()
    {
		$this->display('add');
    }
	

	public function doadd()
    {
	$menu_name = $_POST['menu_name'];
	$action_name = ucwords($_POST['action_name']);
	$table_name = $_POST['table_name'];
	$zj_id = trim($_POST['zj_id']);
	$fields_alis = $_POST['fields_alis'];
	$fields_name = $_POST['fields_name'];
	if($menu_name=='' || $action_name=='' || $table_name=='' ||$zj_id ==''){$this->error('参数不完整');}
	if(count($fields_name)==0 || count($fields_alis) != count($fields_name)){$this->error('字段为空');}
	if(!in_array($zj_id,$fields_name)){$this->error('唯一字段不在表的字段中输入有误!');}
	$action_file = './Public/Mtable/TemplateAction.class.php';
	$index_file = './Public/Mtable/index.html';
	$add_file = './Public/Mtable/add.html';
	$target_action_file = './Admin/Lib/Action/'.$action_name.'Action.class.php';
	if(file_exists($target_action_file)){$this->error('控制器'.$target_action_file.'文件已经存在，<br>覆盖将造成不可预料的后果，程序终止执行，请重新输入控制器名称!');exit();}
	$target_index_file = './Admin/Tpl/default/'.$action_name.'/index.html';
	$target_add_file = './Admin/Tpl/default/'.$action_name.'/add.html';
	//修改控制器
	$configStr = read_file($action_file);
	$configStr = str_replace('TemplateAction',$action_name.'Action',$configStr);
	$configStr = str_replace('table_name',$table_name,$configStr);
	$configStr = str_replace('zj_id',$zj_id,$configStr);
	file_put_contents($target_action_file, $configStr) or die("<script>alert('写入".$target_action_file."失败');history.go(-1);</script>");
	//修改首页模板
	$configStr = read_file($index_file);
	$configStr = str_replace('menu_name',$menu_name,$configStr);
	$col_td_loop='';
	for($i=0;$i<count($fields_name);$i++){
		if(intval($_POST['fields_visible'.$i])==1){
	$col_td_loop .= '<td align="center">'.$fields_alis[$i].'</td>';
		}
	}
	$col_td_loop .= '<td align="center">操作</td>';
	$configStr = str_replace('col_td_loop',$col_td_loop,$configStr);
	$col_td_volist='';
	for($i=0;$i<count($fields_name);$i++){
		if(intval($_POST['fields_visible'.$i])==1){
	$col_td_volist .= '<td align="left">{$vo.'.$fields_name[$i].'}</td>';
		}
	}
	$col_td_volist .= '<td align="center"><a href="__APP__/'.$action_name.'/edit/'.$zj_id.'/{$vo.'.$zj_id.'}">修改</a>&nbsp;<a href="javascript:if(confirm(\'您确定删除吗?\')){ location.href=\'__APP__/'.$action_name.'/del/'.$zj_id.'/{$vo.'.$zj_id.'}\'; }">删除</a></td>';
	$configStr = str_replace('col_td_volist',$col_td_volist,$configStr);
	@chmod(dirname($target_index_file),0777);
	if(!file_exists(dirname($target_index_file))){@mkdir(dirname($target_index_file));}
	file_put_contents($target_index_file, $configStr) or die("<script>alert('写入".$target_index_file."失败');history.go(-1);</script>");
	//修改添加修改模板
	$configStr = read_file($add_file);
	$loop_lb ='';
	for($i=0;$i<count($fields_name);$i++){
	$loop_lb .= '<tr><td class="b1_1">'.$fields_alis[$i].'</td><td colspan="2" class="b1_1"><input name="'.$fields_name[$i].'" type="text" value="{$info.'.$fields_name[$i].'}"></td></tr>';
	}
	$configStr = str_replace('loop_lb',$loop_lb,$configStr);
	$configStr = str_replace('zj_id',$zj_id,$configStr);
	@chmod(dirname($target_add_file),0777);
	if(!file_exists(dirname($target_add_file))){@mkdir(dirname($target_add_file));}
	file_put_contents($target_add_file, $configStr) or die("<script>alert('写入".$target_add_file."失败');history.go(-1);</script>");
	//添加数据
	M('extend_menu')->add(array('menu_name'=>$menu_name,'typeid'=>0,'action_name'=>$action_name,'table_name'=>$table_name,'enable'=>1));
	$this->assign("jumpUrl",U('Mtable/index'));
	$this->success('添加成功!');	
    }
	
	public function del()
    {
		$type = M('extend_menu');
		$action_name = $type->where('id='.$_GET['id'])->getField('action_name');
		@unlink('./Admin/Lib/Action/'.$action_name.'Action.class.php');
		deldir('./Admin/Tpl/default/'.$action_name);
		if($type->where('id='.$_GET['id'])->delete())
		{
			$this->assign("jumpUrl",U('Mtable/index'));
			$this->success('操作成功!');
		}
		$this->error('操作失败!');	
    }
	//ajax获取表字段
function ajax_get_fields(){
$table_name = htmlspecialchars($_REQUEST['table_name']);
$this->ajaxReturn(self::get_mysql_fields($table_name),'ok',1);
}
//获取表字段
function get_mysql_fields($t_name){
$sql = "SHOW TABLES LIKE '{$t_name}'";
$mret = M()->query($sql);
$num = count($mret);
if($num==1) {
$ret = M($t_name,null)->getDbFields();
return $ret;
} else {	
return false;
}
}
//类结束	
}
?>