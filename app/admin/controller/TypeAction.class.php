<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
	
    @function 栏目管理模块

    @Filename TypeAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-23 10:33:18 $
*************************************************************/
class TypeAction extends CommonAction
{	
    public function index()
    {
		$type = M('type');
		$article = M('article');
		$list = $type->field("typeid,typename,ismenu,isindex,islink,isuser,drank,irank,fid,concat(path,'-',typeid) as bpath")->group('bpath,drank')->select();
		//echo $type->getLastSql();
		foreach($list as $k=>$v)
		{
		    $list[$k]['is_last'] = $type->where('fid='.$v['typeid'])->count();
			$list[$k]['count'] = count(explode('-',$v['bpath']));
			$list[$k]['total'] = $article->where('typeid='.$v['typeid'])->count();
			$str = '';
			if($v['fid'] <> 0)
			{
				for($i = 0;$i < $list[$k]['count'] * 2;$i++)
				{
					$str .= '&nbsp;';
				}
				$str .= '|-';
			}
			$list[$k]['space'] = $str;
		}
		$this->assign('list',$list);
		unset($type,$article,$list);
		$this->display('index');	
    }
	//字段显示控制
	function show_field(){
	$typeid = (int)$_REQUEST['typeid'];
	if($typeid ==0){$this->error('错误的分类编号');exit();}
	//字段默认显示
	$arr = array(
	array('txt'=>'标题','show'=>1,'field_name'=>'title'),
	array('txt'=>'关键字','show'=>1,'field_name'=>'keywords'),
	array('txt'=>'描述','show'=>1,'field_name'=>'description'),
	array('txt'=>'作者','show'=>1,'field_name'=>'author'),
	array('txt'=>'来源','show'=>1,'field_name'=>'copyfrom'),
	array('txt'=>'浏览次数','show'=>1,'field_name'=>'hits'),
	array('txt'=>'分类','show'=>1,'field_name'=>'typeid'),
	array('txt'=>'转向链接','show'=>1,'field_name'=>'linkurl'),
	array('txt'=>'缩略图','show'=>1,'field_name'=>'imgurl'),
	array('txt'=>'文章摘要','show'=>1,'field_name'=>'note'),
	array('txt'=>'附件上传','show'=>1,'field_name'=>''),
	array('txt'=>'内容','show'=>1,'field_name'=>'content'),
	array('txt'=>'发布时间','show'=>1,'field_name'=>'addtime'),
	array('txt'=>'附加选项','show'=>1,'field_name'=>'固顶:istop 推荐:ishot 幻灯:isflash'),
	array('txt'=>'自动分页字数','show'=>1,'field_name'=>'pagenum'),
	array('txt'=>'本文显示投票','show'=>1,'field_name'=>'voteid')
	);
	$temp = M('type')->where('typeid='.$typeid)->find();
	
	if($temp){
    $this->assign('field_name',$temp['typename']); 
	$myarr = explode('|',$temp['show_fields']);
	//dump($myarr);
	if(count($myarr) > 10){
	for($i=0;$i<count($myarr);$i++){
	$arr[$i]['show'] = $myarr[$i];
	}	
	}	
	}
	$this->assign('list',$arr);	
	//加载扩展字段不想用JOIN个人认为效率不高
	$list_extend = M('extend_fieldes')->order('orders')->select();
	foreach($list_extend as $k=>$v){
	$is_show = M('extend_show')->where('typeid='.$typeid.' and field_id='.$v['field_id'])->getField('is_show') == 1?1:0;
	$list_extend[$k]['is_show'] = $is_show;
	}
	$this->assign('list_extend',$list_extend);	
	$this->display();		
	}
	//模板控制
	function manage_moban(){
	$typeid = (int)$_REQUEST['typeid'];
	if($typeid ==0){$this->error('错误的分类编号');exit();}
	$temp = M('type')->where('typeid='.$typeid)->find();
	$this->assign('model_path',$temp);
	$this->display();	
	}
	
	//保存字段显示控制
	function doshow_field(){
	$typeid = (int)$_REQUEST['typeid'];
	$nset = (int)$_REQUEST['nset'];
	$outids = htmlspecialchars($_REQUEST['outids']);
	if($typeid ==0){$this->error('错误的分类编号');exit();}    
	$str = join('|',array_slice($_POST, 1,16));
	$data = array();
	$data['typeid'] = $typeid;
	$data['show_fields'] = $str;
	$dao = M('type');
	$temp = $dao->where('typeid='.$typeid)->save($data);
	//保存扩展字段显示
	$list_extend = M('extend_fieldes')->order('orders')->select();
	foreach($list_extend as $k=>$v){
	//不是第一次设置	
	$dao = M('extend_show')->where('typeid='.$typeid.' and field_id='.$v['field_id'])->find();
	if($dao){
	M('extend_show')->where('typeid='.$typeid.' and field_id='.$v['field_id'])->setField('is_show',intval($_POST['field_'.$v['field_id']]));
	}
	else{
	//第一次设置
	$arr['typeid'] = $typeid;
	$arr['field_id'] = $v['field_id'];
	$arr['is_show'] = intval($_POST['field_'.$v['field_id']]) ;
	$arr['orders'] = $v['orders'];
	M('extend_show')->add($arr);
	}	
	}
	//下一级保存该设置
	if($nset==1){
	$where='';
	if($outids!=''){
	$where .= ('typeid not in('.str_replace('|',',',$outids).') and'); 
	}
	$ids = get_children($typeid);
	$where .= "typeid in($ids)";
	M('type')->where($where)->setField('show_fields',$str);
	$childids = M('type')->where($where)->select();
	foreach($childids as $key=>$value){
	foreach($list_extend as $k=>$v){
	$dao = M('extend_show')->where('typeid='.$value['typeid'].' and field_id='.$v['field_id'])->find();
	if($dao){
	M('extend_show')->where('typeid='.$value['typeid'].' and field_id='.$v['field_id'])->setField('is_show',intval($_POST['field_'.$v['field_id']]));
	}
	else{
	$arr['typeid'] = $value['typeid'];
	$arr['field_id'] = $v['field_id'];
	$arr['is_show'] = intval($_POST['field_'.$v['field_id']]) ;
	$arr['orders'] = $v['orders'];
	M('extend_show')->add($arr);
	}
	//echo M('extend_show')->getLastSql();
	}	
	}
	}
	$this->success('保存字段显示成功');
	}
	//保存模板
	function domanage_moban(){
	$typeid = (int)$_REQUEST['typeid'];
	$nset = (int)$_REQUEST['nset'];
	$outids = htmlspecialchars($_REQUEST['outids']);
	if($typeid ==0){$this->error('错误的分类编号');exit();}    
	$data = array();
	$data['typeid'] = $typeid;
	$data['list_path'] = $_REQUEST['list_path'];
	$data['page_path'] = $_REQUEST['page_path'];
	//dump($data);
	$dao = M('type');
	$temp = $dao->where('typeid='.$typeid)->save($data);
	//保存下一级配置
	if($nset==1){
	$where='';
	if($outids!=''){
	$where .= ('typeid not in('.str_replace('|',',',$outids).') and'); 
	}
	$ids = get_children($typeid);
	$where .= "typeid in($ids)";
	M('type')->where($where)->setField(array('list_path'=>$_REQUEST['list_path'],'page_path'=>$_REQUEST['page_path']));
	}
	$this->success('保存模板路劲成功');
	}
	
	public function add()
    {
        $type = M('type');
		$list = $type->where("islink=0")->field("typeid,typename,ismenu,isindex,islink,isuser,drank,irank,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select();
		foreach($list as $key=>$value)
		{
			$list[$key]['count'] = count(explode('-',$value['bpath']));
		}
		$this->assign('list',$list);
		unset($type,$list);
		$this->display('add');
    }
	
	public function doadd()
    {
		if(empty($_POST['typename']))
		{
			$this->error('栏目名称不能为空!');
		}
		//构造上级的字段显示和模型
		if(isset($_POST['fid']) && intval($_POST['fid'])>0){
		$typeid = intval($_POST['fid']);
		$temp = M('type')->where('typeid='.$typeid)->find();
		if($temp){$_POST['show_fields'] = $temp['show_fields'];$_POST['list_path'] = $temp['list_path'];$_POST['page_path'] = $temp['page_path'];}		
		}
		$type = D('Type');
		if($type->create())
		{
		$id = $type->add();
			if($id)
			{
			//构造扩展字段
			$list = M('extend_show')->where('typeid='.$typeid)->select();
			
			foreach($list as $k=>$v){
			$data = array();
			$data = $v;
			unset($data['id']);
			$data['typeid'] = $id;
			M('extend_show')->add($data);
			}
				$this->_log_operation('添加栏目'.$_POST['typename']);
				$this->assign("waitSecond",30);
				$this->assign("jumpUrl",U('Type/index'));
				$this->success('操作成功! 您可以<a href="'.U('Type/add',array('fid'=>$typeid)).'" style="color:green">继续添加</a>&nbsp;&nbsp;<a href="'.U('Type/index').'" style="color:red">返回分类列表</a>');
			}
			$this->_log_operation('添加栏目','失败');
			$this->error('操作失败!');
		}
		$this->_log_operation('添加栏目'.$type->getError(),'失败');
		$this->error($type->getError());
    }

	 public function edit()
    {
    	$type = M('type');
		$list = $type->where('typeid='.intval($_GET['typeid']))->find();
		//获取栏目option
		$olist = $type->where("islink=0")->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select();
		
		foreach($olist as $k=>$v)
		{
			$count[$k] = '';
			$ban ='';
			if($v['fid'] <> 0)
			{
				for($i = 0;$i < count(explode('-',$v['bpath'])) * 2;$i++)
				{
					$count[$k].='&nbsp;';
				}
			}
		
			if($v['typeid']==$_GET['typeid'])
			{
				$ban =" disabled='disabled'";
			}
		
			if($v['typeid'] == $list['fid'])
			{
				$option.="<option value=\"{$v['typeid']}\" selected{$ban}>{$count[$k]}|-{$v['typename']}</option>";
			}
			else
			{
				$option.="<option value=\"{$v['typeid']}\"{$ban}>{$count[$k]}|-{$v['typename']}</option>";
			}
		}

		if($list['fid'] == 0)
		{
			$option.='<option value="0" selected>做为顶级分类</option>';
		}
		else
		{
			$option.='<option value="0">做为顶级分类</option>';
		}
		$this->assign('option',$option);
        $this->assign('list',$list);
		unset($list,$type);
		$this->display('edit');
    }
	
//执行编辑
	public function doedit()
    {
	    if(empty($_POST['typename']))
		{
			$this->error('栏目名称不能为空!');
		}
		$type = D('Type');
		$type->create();
		if($type->save())
		{
			$this->_log_operation('修改栏目'.$_POST['typename']);
			$this->assign("jumpUrl",U('Type/index'));$this->success('操作成功!');
			
		}
		$this->error('操作成功,什么也未改变!');
    }
	
	//删除栏目&执行删除
		public function del()
    {
		$type = M('type');
		$article = M('article');
		if($type->where('fid='.$_GET['typeid'])->select())
		{
			$this->assign("jumpUrl",U('Type/index'));$this->error('请先删除子栏目!');
		}
		if($article->where('typeid='.intval($_GET['typeid']))->select())
		{
			$this->assign("jumpUrl",U('Type/index'));$this->error('请先清空栏目下文章!');
		}
		if($type->where('typeid='.intval($_GET['typeid']))->delete())
		{
		M('extend_show')->where('typeid='.intval($_GET['typeid']))->delete();
			$this->_log_operation('删除栏目ID：'.intval($_GET['typeid']));
		$this->assign("jumpUrl",U('Type/index'));$this->success('删除成功!');
		}
    }
	
	//ajax扩展后台菜单
	function ajax_menuid(){
	$typeid = intval($_REQUEST['typeid']);
	$t = M('extend_menu')->where('typeid='.$typeid)->find();
	if($t){
	$this->ajaxReturn($t,'ok',1);	
	}else{
	$this->ajaxReturn(array(),'err',0);		
	}	
	}
	//ajax保存扩展菜单
	function ajax_domenu(){
	$typeid = intval($_REQUEST['typeid']);
	if($typeid==0){exit('参数错误');}
	$t = M('extend_menu')->where('typeid='.$typeid)->find();
	if($t){
	if(intval($_REQUEST['enable'])==2){
	M('extend_menu')->where('typeid='.$typeid)->delete();	
	}else{	
	M('extend_menu')->where('typeid='.$typeid)->save(array_map("unescape",$_REQUEST));
	}
	}
	else{
	M('extend_menu')->add(array_map("unescape",$_REQUEST));	
	}	
	}

	public function status(){
		$a = M('type');
		$s = explode("-",$_GET['s']);
		if($s[1] == 0)
		{
			$a->where( 'typeid='.$_GET['typeid'] )-> setField($s[0],1);
		}
		elseif($s[1] == 1)
		{
			$a->where( 'typeid='.$_GET['typeid'] )-> setField($s[0],0);
		}
		else
		{
			$this->error('非法操作');
		}
		$this->redirect('index');
	}
}
?>