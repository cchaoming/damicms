<?php

/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 前台列表页 Action
 *
 * @Filename ListAction.class.php $
 *
 * @Author 追影 生成HTML 写的不够灵活 有需要的根据自己需求修正 QQ:279197963 $
 *
 * @Date 2011-11-18 08:42:11 $
 *************************************************************/
namespace app\home\controller;
use app\base\model\ArticleView;
use think\facade\Db;
use until\Page;

class Lists extends Base
{
    public function index()
    {
        //读取数据库&判断
        $typeid = (int)$this->request->param('typeid',0);
        if(!$typeid){$this->error('栏目不存在!');}
        $list_model = 'list/list_default.html';
        $type = M('type');
        $list = $type->whereRaw('typeid=' .$typeid )->find();
        if (!$list) {
            $this->error('栏目不存在!');
        } else {
            //当前选中菜单
            if($list['url'] && $list['islink'] == 1){
                header("Location:".$list['url']);
            }
            $pid = get_first_father($list['typeid']);
            $cur_menu = get_field('type', 'typeid=' . $pid, 'drank');
            $this->assign('cur_menu', $cur_menu);
            if ($list['list_path'] != '' && file_exists(TMPL_PATH . cookie('think_template') . '/' . $list['list_path'])) {
                $list_model = $list['list_path'];
            }
        }
        ob_start();
//家族树与子孙树
        parent::tree_dir($typeid, 'tree_list');
        parent::children_dir($typeid, 'child_list');
//栏目基本信息封装
        $this->assign('title', $list['typename']);
        if ($list['keywords'] != "") {
            $this->assign('keywords', $list['keywords']);
        }
        if ($list['description'] != "") {
            $this->assign('description', $list['description']);
        }
        $this->assign('type', $list);
//栏目导航
        $config = config('basic');
        if ($config['listshowmode'] == 1) {
            $map[] = ['fid','=',$list['fid']];
        } else {
            $map[] = ['fid','=',intval($_GET['typeid'])];
        }
        $map[] = ['islink','=',0];
        $nav = $type->removeOption()->where($map)->field('typeid,typename')->select()->toArray();
        $this->assign('dh', $nav);
//第一次释放内存
        $pernum = (isset($list['pernum']) && intval($list['pernum']) > 0) ? intval($list['pernum']) : $config['artlistnum'];
        unset($list, $nav, $map);
        $list_server = M('admin')->whereRaw('is_client=1')->select()->toArray();
        $this->assign('list_server', $list_server);
        $vip_sn = M('vip_mess')->limit(1)->orderRaw('id desc')->value('vip_sn');
        $this->assign('vip_sn', $vip_sn);
//查询数据库和缓存
        $article = new ArticleView();
        $fields_arr = array_keys(Db::getFields('dami_article'));
        $fields_no = array('aid','typeid');
        foreach($_REQUEST as $k=>$v){
            if(in_array($k,$fields_arr) && $v !='' && !in_array($k,$fields_no)){
                $map[$k] = array('like','%'.inject_check(urldecode($v)).'%');
            }
        }
//封装条件
        $map[] = ['article.status','=',1];
//准备工作
        $arr = get_children($typeid);
        $map[] = ['article.typeid','in', $arr];
        //用户阅读权限
        if ($config['isread'] == 1) {
            $map[] = ['article.typeid','in',explode(',',session('dami_uservail'))];
        }
        //分页处理
        $count = $article->getTableInstance()->where($map)->select()->count();
        $p = new Page($count, $pernum);
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '篇文章');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        if (check_wap()) {
            $temp_str = "%first%%upPage%%downPage%%end%";
        } else {
            $temp_str = "%first%%upPage%%prePage%%linkPage%%nextPage%%downPage%%end%
		<select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select>共<font color='#CD4F07'><b>%totalRow%</b></font>篇 " . $pernum . "篇/每页";
        }
        $p->setConfig('theme', $temp_str);
//数据查询
        $alist = $article->getTableInstance()->where($map)->orderRaw('istop desc,addtime desc')->limit($p->firstRow, $p->listRows)->select()->toArray();
        //echo $article->getLastSql();
//封装变量
        $this->assign('page', $p->show());
        $this->assign('list', $alist);
//释放内存
        unset($article, $type, $p, $tlist, $alist);
//模板输出
        return $this->display($this->template . $list_model);
    }

    function photo()
    {
        $count = M('article')->whereRaw('is_from_mobile=1 and imgurl<>\'\'')->count();
        $this->assign('count', $count);
        $list = M('article')->removeOption()->whereRaw('is_from_mobile=1 and imgurl<>\'\'')->select()->toArray();
        $this->assign('list', $list);
        return $this->display($this->template . '/photo.html');
    }
}