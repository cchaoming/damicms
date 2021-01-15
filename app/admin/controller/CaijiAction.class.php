<?php

/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 文章管理
 *
 * @Filename ArticleAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-27 08:52:44 $
 *************************************************************/
class CaijiAction extends CommonAction
{
	public $file = './Public/Uploads/dami_caiji.sql';
    function caiji()
    {
        $sql = "describe " . C('DB_PREFIX') . "article;";
        $list_field = M()->query($sql);
        //加载缓存数据
        $form_data = F('splider_form');
        if($form_data){
            $form_data = json_decode($form_data,true);
            //var_dump($form_data);
        }
        $this->assign('form_data', $form_data);
        $selected = intval($form_data['typeid'])>0?intval($form_data['typeid']):null;
        $this->addop($selected);
        $this->assign('list_field', $list_field);
        $this->display();
    }

//开始采集
    function docaiji()
    {
        set_time_limit(0);
        import('ORG.Util.Spider');
        $form_data_str = json_encode($_POST);
        F('splider_form',$form_data_str);
        $islocal = intval($_POST['islocal']);
        $list_url = trim($_POST['url_list']);
        $charset = trim($_POST['charset']);
        $page_url = trim($_POST['page_list']);
        $act = intval($_POST['act']);
        $field = $_POST['field'];
        $field[] = 'typeid';//附加的无需匹配的字段
        $field[] = 'addtime';
        $role = $_POST['role'];
        $role[] = $_POST['typeid'];
        $role[] = date('Y-m-d H:i:s');
        $spider = new spider();
//支持单页或多页采集
        $spider->islocal = $islocal;
        $spider->addStartUrl($list_url);
        $spider->setCharset($charset);
        $spider->addLayer(0, 'list', $page_url);
        for ($i = 0; $i < count($field); $i++) {
            $spider->addField($field[$i], $role[$i]);
        }
        $spider->run();
        $spider->output();
        //$file = './Public/Uploads/dami_caiji.sql';
        $spider->saveSql('dami_article', $this->file, $act);
    }
//预览sql
    public function review(){
        $form_data = F('splider_form');
        if($form_data){
            $form_data = json_decode($form_data,true);
        }else{
            alert("还未做过采集!", 1);
        }
       // $file = $_SERVER['DOCUMENT_ROOT'] . '/dami_caiji.sql';
        if(!file_exists($this->file)){alert("未找到SQL文件!", 1);}
        $fp=fopen($this->file,'r');
        $data = '';
        for ($i=0;$i<3;$i++){
            if(!feof()){
            if($form_data['charset'] != 'utf-8'){
                $data .= iconv('GBK', 'UTF-8', fgets($fp));
            }else{
                $data .= fgets($fp);
            }
            }
        }
        $this->assign('data', $data);
        $this->display();
    }
//删除
    public function del_sqlfile(){
		@unlink($this->file);
		$this->success('删除SQL文件成功');
	}
//文章模块 添加-栏目option
    private function addop($selected = null)
    {
        $type = M('type');
        //获取栏目option
        $list = $type->where('islink=0')->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select();
        foreach ($list as $k => $v) {

            $check = '';
            if (isset($_REQUEST['typeid'])) {
                if ($v['typeid'] == intval($_REQUEST['typeid'])) {
                    $check = 'selected="selected"';
                }
            }
            if($selected && $v['typeid'] == $selected){
                $check = 'selected="selected"';
            }

            if ($v['fid'] == 0) {
                $count[$k] = '';
            } else {
                for ($i = 0; $i < count(explode('-', $v['bpath'])) * 2; $i++) {
                    $count[$k] .= '&nbsp;';
                }
            }
            $option .= "<option value=\"{$v['typeid']}\" $check>{$count[$k]}|-{$v['typename']}</option>";
        }
        $this->assign('option', $option);
    }
//结束 
}

?>