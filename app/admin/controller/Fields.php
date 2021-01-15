<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
	
    @function 栏目管理模块

    @Filename TypeAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:33:18 $
*************************************************************/
namespace app\admin\controller;
use think\exception\ValidateException;
use think\facade\Db;

class Fields extends Common
{	
    public function index()
    {
		$dao = M('extend_fieldes');
		$list = $dao->orderRaw('orders asc')->select()->toArray();
		$this->assign('list',$list);
		return $this->display('index');
    }
	//添加扩展字段
	function add(){
	$this->assign('list_type',config('app.FIELD_TYPE'));
	return $this->display();
	}	
	//保存添加扩展字段
	function doadd(){
	$dao = M('extendFieldes',true);
	if($_POST['field_name'] == ''){$this->error('请输入字段名称');}
	$data = $_POST;
        try {
            validate(\app\base\validate\ExtendFieldes::class)->check($data);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            $this->error($e->getError());
        }
	if(trim($_POST['options']) !=''){
	$data['set_option'] = str_replace("\r\n",',',trim($_POST['options']));
	}
    $prefix = (string)config('database.connections.mysql.prefix');
	$sql = "ALTER TABLE {$prefix}article ADD ".trim($_POST['field_name'])." text(0) CHARACTER SET utf8;";
	$t = Db::execute($sql);
	if($t !== false){
	$dao->save($data);
	$this->assign("waitSecond",30);
	$this->assign("jumpUrl",U('Fields/index'));
	$this->success('添加扩展字段成功! 您可以<a href="'.U('Fields/add').'" style="color:green">继续添加</a>&nbsp;&nbsp;<a href="'.U('Fields/index').'" style="color:red">返回扩展字段列表</a>');
	}
	$this->error('已经存在该字段或数据非法!');
	}
	//删除
	function del(){
    $prefix = (string)config('database.connections.mysql.prefix');
	$field_id = (int)$_REQUEST['field_id'];
	$sql = "ALTER TABLE {$prefix}article DROP COLUMN ".trim($_REQUEST['field_name']);
	$t = Db::execute($sql);
	if($t !== false){
	M('extendFieldes',true)->whereRaw('field_id='.$field_id)->delete();
	M('extend_show')->whereRaw('field_id='.$field_id)->delete();
	$this->success('删除成功',U('Fields/index'));
	}	
	$this->error('删除失败');
	}
	//编辑
	function edit(){
	$field_id = (int)$_REQUEST['field_id'];
	$this->assign('list_type',config('app.FIELD_TYPE'));
	$list = M('extend_fieldes')->whereRaw('field_id='.$field_id)->find();
	if($list['field_type']>2 && $list['field_type']<7){
	$list['set_option'] = str_replace(',',"\r\n",$list['set_option']);	
	}
	$this->assign('list',$list);
	return $this->display();
	}
	//保存编辑
	function doedit(){
	$field_id = (int)$_REQUEST['field_id'];
	$dao = M('extendFieldes',true);
	if($_POST['field_name'] == ''){$this->error('请输入字段名称');exit();}
	$list = $dao->whereRaw('field_id='.$field_id)->find();
	if(!$list){$this->error('找不到该字段');exit();}
	$data = $_POST;
        try {
            validate(\app\base\validate\ExtendFieldes::class)->check($data);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            $this->error($e->getError());
        }
	if(trim($_POST['options']) !=''){
	$data['set_option'] = str_replace("\r\n",',',trim($_POST['options']));
	}
	if($list['field_name'] != trim($_POST['field_name'])){
    $prefix = (string)config('database.connections.mysql.prefix');
	$sql = "ALTER TABLE {$prefix}article change ".$list['field_name']." ".trim($_POST['field_name'])." text(0) CHARACTER SET utf8;";
	Db::execute($sql);
    }
	$list->save($data);
	M('extend_show')->whereRaw('field_id='.$field_id)->save(['orders'=>$_POST['orders']]);
	$this->success('修改成功',U('Fields/index'));
    }
	
}
?>