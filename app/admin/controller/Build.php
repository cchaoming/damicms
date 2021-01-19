<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 文章生成管理
 *
 * @Filename ArticleAction.class.php $
 *
 * @Author 追影 QQ:279197963 $ 适用隐藏index.php并开启啦伪静态的
 *
 * @Date 2011-11-27 08:52:44 $
 *************************************************************/

namespace app\admin\controller;
use think\facade\Session;

class Build extends Common
{
    //配置
    private $url_path, $theme, $do_all;

    function initialize()
    {
        parent::initialize();
        $this->url_path = dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
        $this->theme = (isset($_REQUEST['theme']) && $_REQUEST['theme'] != '') ? trim($_REQUEST['theme']) : 'default';
        cookie('think_template', $this->theme);
    }

    function index()
    {
        $type = M('type');
        $oplist = $type->whereRaw('islink=0')->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select()->toArray();
        $op ='';
        foreach ($oplist as $k => $v) {
            $check = '';
            if (!is_null($this->request->param('typeid'))) {
                if ($v['typeid'] == intval($this->request->param('typeid'))) {
                    $check = 'selected="selected"';
                }
            }
            $count[$k] = '';
            if ($v['fid'] != 0) {
                for ($i = 0; $i < count(explode('-', $v['bpath'])) * 2; $i++) {
                    $count[$k] .= '&nbsp;';
                }
            }
            $op .= "<option value=\"" . $v['typeid'] . "\" $check>{$count[$k]}|-{$v['typename']}</option>";
        }
        $this->assign('op', $op);
        return $this->display();
    }

    //生成首页
    function build_index()
    {
        Session::set('is_rebuild', 1);
        $url = $this->url_path . "/index.php/home/Index/index";
        get_url_contents($url);
        Session::set('is_rebuild', NULL);
        if ($this->do_all != 1) {
            $this->ajaxReturn('首页生成成功!', '提示', 1);
        }
    }

//生成列表页
    function build_list()
    {
        $typeid = $this->request->param('typeid',0);
        $type_arr = self::pub_type($typeid);
        $list_per = M('config')->whereRaw('id=1')->value('artlistnum');
        $this->assign('type_arr', $type_arr);
        $this->assign('list_per', $list_per);
        return $this->display();
    }

//生成文章
    function build_art()
    {
        $typeid = $this->request->param('typeid',0);
        $type_arr = self::pub_type($typeid);
        $list = M('article')->whereRaw('typeid in(' . join(',', $type_arr) . ')')->select()->toArray();
        $this->assign('list', $list);
        return $this->display();
    }

//返回typeid
    private function pub_type($typeid)
    {
        $type_arr = array();
        if ($typeid > 0) {
            $type_arr = explode(',', get_children($typeid));
        } else {
            $all_type = M('type')->whereRaw('islink=0')->select()->toArray();
            foreach ($all_type as $k => $v) {
                $type_arr[] = $v['typeid'];
            }
        }
        return $type_arr;
    }
//结束 
}

?>