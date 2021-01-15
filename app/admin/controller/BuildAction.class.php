<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 文章生成管理

    @Filename ArticleAction.class.php $

    @Author 追影 QQ:279197963 $ 适用隐藏index.php并开启啦伪静态的

    @Date 2011-11-27 08:52:44 $
*************************************************************/
class BuildAction extends CommonAction
{	
 //配置
private $url_path,$theme,$do_all;
function _initialize(){ 
parent::_initialize();
$this->url_path = dirname('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
$this->theme = (isset($_REQUEST['theme']) && $_REQUEST['theme'] !='')?trim($_REQUEST['theme']):'default';
cookie('think_template',$this->theme);
}
function index(){
 		$type = M('type');
		$oplist = $type->where('islink=0')->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select();
		foreach($oplist as $k=>$v)
		{
		$check ='';
		if(isset($_REQUEST['typeid']))
		{
		if($v['typeid'] == intval($_REQUEST['typeid'])){
		$check = 'selected="selected"';
		}
		}
			if($v['fid'] == 0)
			{
				$count[$k]='';
			}
			else
			{
				for($i = 0;$i < count(explode('-',$v['bpath'])) * 2;$i++)
				{
					$count[$k].='&nbsp;';
				}
			}
			$op.="<option value=\"".$v['typeid']."\" $check>{$count[$k]}|-{$v['typename']}</option>";
		}
 $this->assign('op',$op);
 $this->display();
 }
 //生成首页
function build_index(){
Session::set('is_rebuild',1);
$url =  $this->url_path."/index.php?m=Index&a=index";
file_get_contents($url);
Session::set('is_rebuild',NULL);
if($this->do_all !=1){
$this->ajaxReturn('首页生成成功!','提示',1);
}
}
//生成列表页
function build_list(){
$typeid = (isset($_REQUEST['typeid'])&&$_REQUEST['typeid']!='')?intval($_REQUEST['typeid']):0;
$type_arr = self::pub_type($typeid);
$list_per = M('config')->where('id=1')->getField('artlistnum');
$this->assign('type_arr',$type_arr);
$this->assign('list_per',$list_per);
$this->display();
}
//生成文章
function build_art(){
$typeid = (isset($_REQUEST['typeid'])&&$_REQUEST['typeid']!='')?intval($_REQUEST['typeid']):0;
$type_arr = self::pub_type($typeid);
$list = M('article')->where('typeid in('.join(',',$type_arr).')')->select();
$this->assign('list',$list);
$this->display();
}
//返回typeid
private function pub_type($typeid){
$type_arr = array();
if($typeid >0){
$type_arr = explode(',',get_children($typeid));
}
else
{
$all_type = M('type')->where('islink=0')->select();
foreach($all_type as $k=>$v){
$type_arr[] = $v['typeid'];
}
}
return $type_arr;
}
//结束 
}
?>