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
namespace app\admin\controller;
use until\Page;

class Article extends Common
{
    public function index()
    {
        $this->type_tree();
        $article = D('ArticleView');
        $condtion = '1=1';
        $typeid = 0;
        if ($this->request->param('typeid')) {
            $typeid = intval($this->request->param('typeid'));
        } else if (cookie('?curr_typeid')) {
            $typeid = intval(cookie('curr_typeid'));
        }
        if ($typeid > 0) {
            $is_last = 0;
            //这里其实不完善没有查找子类的文章
            $arr = get_children($typeid);
            $condtion .= ' and article.typeid in(' . $arr . ')';
            //判定是否为最底层栏目
            $t_num = M('type')->whereRaw('islink=0 and fid=' . $typeid)->count();
            if ($t_num == 0) {
                $is_last = 1;
            } else {
                $fid = M('type')->whereRaw('islink=0 and typeid=' . $typeid)->value('fid');
                if ($fid > 0 && $t_num > 0) {
                    $is_last = 1;
                }
            }
            if ($is_last == 1) {
                $this->assign('is_last', '1');
                cookie('curr_typeid', $typeid);
            }
        }

        //权限
        if (!session(config('app.ADMIN_AUTH_KEY')) && session(config('app.USER_CONTENT_KEY'))) {
            $condtion .= ' and article.typeid in(' . session(config('app.USER_CONTENT_KEY')) . ')';
        }

        if (isset($_GET['status'])) {
            $condtion .= ' and status=' . $_GET['status'];
        }
        if (isset($_GET['istop'])) {
            $condtion .= ' and istop=' . $_GET['istop'];
        }
        if (isset($_GET['ishot'])) {
            $condtion .= ' and ishot=' . $_GET['ishot'];
        }
        if (isset($_GET['isflash'])) {
            $condtion .= ' and isflash=' . $_GET['isflash'];
        }
        if (isset($_GET['isimg'])) {
            $condtion .= ' and isimg=' . $_GET['isimg'];
        }
        if (isset($_GET['islink'])) {
            $condtion .= ' and islink=' . $_GET['islink'];
        }
        if (isset($_GET['hits'])) {
            $order = 'hits desc';
        } else {
            $order = 'addtime desc';
        }
        $count = $article->getTableInstance()->whereRaw($condtion)->count();
        $p = new Page($count, 20);
        $list = D('ArticleView')->getTableInstance()->whereRaw($condtion)->orderRaw($order)->limit($p->firstRow,$p->listRows)->select()->toArray();
        //echo 	$article->getLastSql();
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '篇文章');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>");
        $this->assign('page', $p->show());
        $this->assign('list', $list);
        $this->moveop();//文章编辑option
        $this->jumpop();//快速跳转option
        return $this->display('index');
    }

    public function add()
    {
        if (!$this->request->param('typeid')) {
            $this->error('请先选择您要添加文章的栏目!');
            exit();
        }
        $typeid = intval($this->request->param('typeid'));
        //默认显示字段
        $str = "1|1|1|1|1|1|0|0|1|1|1|1|1|1|0|0";
        $olist = M('type')->whereRaw("show_fields<>'' and typeid=" . $typeid)->find();
        if ($olist) {
            $str = $olist['show_fields'];
        }
        $arr = explode('|', $str);
        $this->assign('arr', $arr);
        $this->addop();//文章编辑option
        $this->jumpop();//快速跳转option
        $this->vote(0);
        //加载扩展字段	
        $extend_field = list_extend_field($typeid);
        $this->assign('extend_field', $extend_field);
        return $this->display('add');
    }

    public function edit()
    {
        $aid = $this->request->param('aid');
        if(!$aid){$this->error('参数错误');}
        $type = M('article');
        $list = $type->whereRaw('aid=' . $_GET['aid'])->find();
        $typeid = $list['typeid'];
        $str = "1|1|1|1|1|1|0|0|1|1|1|1|1|1|0|0";
        $olist = M('type')->whereRaw("show_fields<>'' and typeid=" . $typeid)->find();
        if ($olist) {
            $str = $olist['show_fields'];
        }
        $arr = explode('|', $str);
        $this->assign('arr', $arr);
        $this->assign('list', $list);
        $this->editop();//文章编辑option
        $this->jumpop();//快速跳转option
        $this->vote($list['voteid']);
        //加载扩展字段	
        $extend_field = list_extend_field($typeid);
        $this->assign('extend_field', $extend_field);
        return $this->display();
    }


    public function doedit()
    {
        $aid = $this->request->param('aid',0);
        if(!$aid){$this->error('参数错误!');}
        if($this->request->isPost()){
        if (empty($_POST['title'])) {
            $this->_log_operation('修改文章标题为空','失败');
            $this->error('标题不能为空!');
        }
        if (empty($_POST['typeid'])) {
            $this->_log_operation('修改文章未选择栏目','失败');
            $this->error('请选择栏目!');
        }
        $maybe_fields = ['title','linkurl','imgurl','voteid','pagenum','hits','typeid'];
        foreach ($maybe_fields as $field_name){
            if ($this->request->param($field_name)) {
                $data[$field_name] = $this->request->param($field_name);
            }
        }
        if (!empty($_POST['TitleFontColor'])) {
            $data['titlecolor'] = trim($_POST['TitleFontColor']);
        }
        $data['aid'] = $aid;
        $data['content'] = $this->request->param('content');
		if(intval(config('app.LOCAL_REMOTE_PIC')) == 1 && $data['content']){
			$data['content'] = local_remotepic($data['content']);
		}
        empty($_POST['addtime']) ? $data['addtime'] = date('Y-m-d H:i:s') : $data['addtime'] = trim($_POST['addtime']);
        empty($_POST['author']) ? $data['author'] = '未知' : $data['author'] = trim($_POST['author']);
        empty($_POST['keywords']) ? $data['keywords'] = '' : $data['keywords'] = str_replace('，',',',trim($_POST['keywords']));
        empty($_POST['description']) ? $data['description'] = '' : $data['description'] = trim($_POST['description']);
        empty($_POST['copyfrom']) ? $data['copyfrom'] = '' : $data['copyfrom'] = trim($_POST['copyfrom']);
        empty($_POST['islink']) ? $data['islink'] = '0' : $data['islink'] = trim($_POST['islink']);
        empty($_POST['istop']) ? $data['istop'] = '0' : $data['istop'] = trim($_POST['istop']);
        empty($_POST['isimg']) ? $data['isimg'] = '0' : $data['isimg'] = trim($_POST['isimg']);
        empty($_POST['ishot']) ? $data['ishot'] = '0' : $data['ishot'] = trim($_POST['ishot']);
        empty($_POST['isflash']) ? $data['isflash'] = '0' : $data['isflash'] = trim($_POST['isflash']);
        //过滤掉[dami_page]
        if (isset($_POST['content'])) {
            $notes = str_replace("[dami_page]", "", $_POST['content']);
            empty($_POST['note']) ? $data['note'] = htmlsubstr($notes, 300) : $data['note'] = trim($_POST['note']);
        }
        //tag处理
        if($data['keywords'] !=''){
            $old_tag = M('article')->whereRaw('aid='.$data['aid'])->value('keywords');
            if($old_tag != $data['keywords']) {
                $koldarr = explode(',',$old_tag);
                $karr = explode(',', $data['keywords']);
                //新增了某些tag
                foreach ($karr as $k => $v) {
                    if(!in_array($v,$koldarr)) {
                        $t = M('tag')->whereRaw("typeid=" . $data['typeid'] . " and tag_name='{$v}'")->find();
                        if ($t) {
                            M('tag')->whereRaw("tag_id=" . $t['tag_id'])->setInc('num', 1);
                        } else {
                            $tag_data['typeid'] = $data['typeid'];
                            $tag_data['tag_name'] = $v;
                            $tag_data['num'] = 1;
                            M('tag')->add($tag_data);
                        }
                    }
                }
                //删除了某些tag
                foreach($koldarr as $k=>$v){
                    if(!in_array($v,$karr)){
                        $t = M('tag')->whereRaw("typeid=" . $data['typeid'] . " and tag_name='{$v}'")->find();
                        if ($t) {
                            if($t[num]>1){
                                M('tag')->whereRaw("tag_id=" . $t['tag_id'])->setDec('num', 1);
                            }else{
                                M('tag')->whereRaw("tag_id=" . $t['tag_id'])->delete();
                            }
                        }
                    }
                }
            }
        }
        //扩展字段数据
        $list_extend = list_extend_field(intval($_POST['typeid']));
        foreach ($list_extend as $k => $v) {
            if (isset($_POST[$v['field_name']])) {
                if (is_array($_POST[$v['field_name']])) {
                    $data[$v['field_name']] = trim(join(',', $_POST[$v['field_name']]));
                } else {
                    $data[$v['field_name']] = trim($_POST[$v['field_name']]);
                }
            }
        }
        $article = M('article',true);
        if ($article->whereRaw('aid='.$data['aid'])->save($data)) {
            //echo $article->getLastSql;
            $this->_log_operation('修改文章ID:'.$data['aid'].'成功');
            $this->assign("jumpUrl", U('Article/index'));
            $this->success('操作成功!');
            exit();
        }
        $this->_log_operation('修改文章ID:'.$data['aid'].'失败','失败');
        $this->error('操作失败或什么也没改变!');
        }
    }


    public function doadd()
    {
        if(!$this->request->isPost()){exit();}
        //验证
        if (empty($_POST['typeid'])) {
            $this->_log_operation('发布文章失败未选择栏目','失败');
            $this->error('请选择栏目!');
        }

        $maybe_fields = ['title','linkurl','imgurl','voteid','pagenum','hits','typeid'];
        foreach ($maybe_fields as $field_name){
            if ($this->request->param($field_name)) {
                $data[$field_name] = $this->request->param($field_name);
            }
        }
        if (!empty($_POST['TitleFontColor'])) {
            $data['titlecolor'] = trim($_POST['TitleFontColor']);
        }
        $data['status'] = 1;
        //使用stripslashes 反转义,防止服务器开启自动转义
        if (isset($_POST['content'])) {
            $data['content'] = $_POST['content'];
			if(intval(config('app.LOCAL_REMOTE_PIC')) == 1){
			$data['content'] = local_remotepic($data['content']);
		    }
        }
        empty($_POST['addtime']) ? $data['addtime'] = date('Y-m-d H:i:s') : $data['addtime'] = trim($_POST['addtime']);
        empty($_POST['author']) ? $data['author'] = '未知' : $data['author'] = trim($_POST['author']);
        empty($_POST['keywords']) ? $data['keywords'] = '' : $data['keywords'] = str_replace('，',',',trim($_POST['keywords']));
        //自动提取介绍
        empty($_POST['description']) ? $data['description'] = '' : $data['description'] = trim($_POST['description']);
        empty($_POST['copyfrom']) ? $data['copyfrom'] = '' : $data['copyfrom'] = trim($_POST['copyfrom']);
        empty($_POST['islink']) ? $data['islink'] = '0' : $data['islink'] = trim($_POST['islink']);
        empty($_POST['istop']) ? $data['istop'] = '0' : $data['istop'] = trim($_POST['istop']);
        empty($_POST['isimg']) ? $data['isimg'] = '0' : $data['isimg'] = trim($_POST['isimg']);
        empty($_POST['ishot']) ? $data['ishot'] = '0' : $data['ishot'] = trim($_POST['ishot']);
        empty($_POST['isflash']) ? $data['isflash'] = '0' : $data['isflash'] = trim($_POST['isflash']);
        //过滤掉[dami_page]
        if (isset($_POST['content'])) {
            $notes = str_replace("[dami_page]", "", $_POST['content']);
            empty($_POST['note']) ? $data['note'] = htmlsubstr($notes, 300) : $data['note'] = trim($_POST['note']);
        }
        //tag处理
        if($data['keywords'] !=''){
            $karr = explode(',',$data['keywords']);
            foreach($karr as $k=>$v){
                $t = M('tag')->whereRaw("typeid=".$data['typeid']." and tag_name='{$v}'")->find();
                if($t){
                    M('tag')->whereRaw("tag_id=".$t['tag_id'])->inc('num',1)->update();
                }else{
                    $tag_data['typeid'] = $data['typeid'];
                    $tag_data['tag_name'] = $v;
                    $tag_data['num'] = 1 ;
                    M('tag',true)->save($tag_data);
                }
            }
        }
        //扩展字段数据
        $list_extend = list_extend_field(intval($_POST['typeid']));
        foreach ($list_extend as $k => $v) {
            if (isset($_POST[$v['field_name']])) {
                if (is_array($_POST[$v['field_name']])) {
                    $data[$v['field_name']] = trim(join(',', $_POST[$v['field_name']]));
                } else {
                    $data[$v['field_name']] = trim($_POST[$v['field_name']]);
                }
            }
        }
        $article = M('article',true);
        if ($article->save($data)) {
            $this->_log_operation('发布文章：'.$data['title'].'成功');
            $this->assign("waitSecond", 30);
            $this->assign("jumpUrl", U('Article/index'));
            $this->success('发布文章成功! 您可以<a href="' . U('Article/add', array('typeid' => intval($_POST['typeid']))) . '" style="color:green">继续发布</a>&nbsp;&nbsp;<a href="' . U('Article/index') . '" style="color:red">返回文章列表</a>');
        }
        $this->_log_operation('发布文章：'.$data['title'].'失败','失败');
        $this->error('操作失败!');
    }


    public function del()
    {
        $aid = $this->request->param('aid',0);
        if(!$aid){$this->error('参数错误!');}
        $article = D('article');
        if ($article->whereRaw('aid='.$aid)->delete()) {
            $this->assign("jumpUrl", U('Article/index'));
            $this->_log_operation('删除文章ID：'.$aid);
            $this->success('操作成功!');
        }
        $this->_log_operation('删除文章ID：'.$aid,'失败');
        $this->error('操作失败!');
    }

    public function status()
    {
        $aid = $this->request->param('aid');
        if($aid){
        $a = M('article');
        if ($_GET['status'] == 0) {
            $this->_log_operation('审核通过文章ID：'.(int)$aid);
            $a->whereRaw('aid=' .$aid)->save(['status'=>1]);
        } elseif ($_GET['status'] == 1) {
            $this->_log_operation('禁用文章ID：'.(int)$aid);
            $a->whereRaw('aid=' . $aid)->save(['status'=>0]);
        } else {
            $this->error('非法操作');
        }
        }
        $this->success('操作成功',U('Article/index'));
    }


    public function delall()
    {
        $aid = $_REQUEST['aid'];  //获取文章aid
        $aids = implode(',', $aid);//批量获取aid
        $id = is_array($aid) ? $aids : $aid;
        $map['aid'] = array('in', $id);
        if (!$aid) {
            $this->error('请勾选记录!');
        }
        $article = D('article');

        if ($_REQUEST['Del'] == '更新时间') {
            $data['addtime'] = date('Y-m-d H:i:s');
            if ($article->where($map)->save($data) !== false) {
                $this->_log_operation('批量更新文章'.$aids.'时间成功');
                $this->assign("jumpUrl", U('Article/index'));
                $this->success('操作成功!');
            }
            $this->_log_operation('批量更新文章'.$aids.'时间','失败');
            $this->error('操作失败!', 1);
        }

        if ($_REQUEST['Del'] == '删除') {
            foreach ($aid as $v) {
                D('article')->whereRaw('aid='.$v)->delete();
            }
            $this->_log_operation('批量删除文章'.$aids.'成功');
            $this->assign("jumpUrl", U('Article/index'));
            $this->success('操作成功!');
        }

        if ($_REQUEST['Del'] == '批量未审') {
            $data['status'] = 0;
            if (D('article')->where($map)->save($data) !== false) {
                $this->_log_operation('批量待审文章'.$aids.'成功');
                $this->assign("jumpUrl", U('Article/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }

        if ($_REQUEST['Del'] == '批量审核') {
            $data['status'] = 1;
            if (D('article')->where($map)->save($data) !== false) {
                $this->_log_operation('批量审核文章'.$aids.'成功');
                $this->assign("jumpUrl", U('Article/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }

        if ($_REQUEST['Del'] == '推荐') {
            $data['ishot'] = 1;
            if (D('article')->where($map)->save($data) !== false) {
                $this->_log_operation('批量推荐文章'.$aids.'成功');
                $this->assign("jumpUrl", U('Article/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }

        if ($_REQUEST['Del'] == '解除推荐') {
            $data['ishot'] = 0;
            if (D('article')->where($map)->save($data) !== false) {
                $this->_log_operation('批量解除审核文章'.$aids.'成功');
                $this->assign("jumpUrl", U('Article/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }

        if ($_REQUEST['Del'] == '固顶') {
            $data['istop'] = 1;
            if (D('article')->where($map)->save($data) !== false) {
                $this->_log_operation('批量固顶文章'.$aids.'成功');
                $this->assign("jumpUrl", U('Article/index'));
                $this->success('操作成功!');
            }
            $this->_log_operation('批量固顶文章'.$aids.'失败','失败');
            $this->error('操作失败!');
        }

        if ($_REQUEST['Del'] == '解除固顶') {
            $data['istop'] = 0;
            if (D('article')->where($map)->save($data) !== false) {
                $this->_log_operation('批量解除固顶文章'.$aids.'成功');
                $this->assign("jumpUrl", U('Article/index'));
                $this->success('操作成功!');
            }
            $this->_log_operation('批量解除固顶文章'.$aids,'失败');
            $this->error('操作失败!');
        }

        if ($_REQUEST['Del'] == '幻灯') {
            $data['isflash'] = 1;
            if (D('article')->where($map)->save($data) !== false) {
                $this->_log_operation('批量幻灯文章'.$aids.'成功');
                $this->assign("jumpUrl", U('Article/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }

        if ($_REQUEST['Del'] == '解除幻灯') {
            $data['isflash'] = 0;
            if (D('article')->where($map)->save($data) !== false) {
                $this->_log_operation('批量解除幻灯文章'.$aids.'成功');
                $this->assign("jumpUrl", U('Article/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }

        if ($_REQUEST['Del'] == '移动') {
            if (intval($_REQUEST['typeid']) == 0) {
                $this->error('操作失败,请选择目标类别！');
            }
            $data['typeid'] = $_REQUEST['typeid'];
            if (D('article')->where($map)->save($data) !== false) {
                $this->_log_operation('批量移动文章'.$aids.'成功');
                $this->assign("jumpUrl", U('Article/index'));
                $this->success('移动成功!');
            }
            $this->error('操作失败!');
        }

        if ($_REQUEST['Del'] == '复制') {
            if (intval($_REQUEST['typeid']) == 0) {
                $this->error('操作失败,请选择目标类别！');
            }
            $list = D('article')->where($map)->select()->toArray();
            foreach ($list as $k => $v) {
                $data = $v;
                $data['aid'] = NULL;
                $data['typeid'] = (int)$_REQUEST['typeid'];
                D('article')->save($data);
            }
            $this->_log_operation('复制成功');
            $this->success('复制成功!');
        }
    }

    //文章模块 批量移动option
    private function moveop()
    {
        $type = M('type');
        $where = '';
        if (!session(config('app.ADMIN_AUTH_KEY')) && session(config('app.USER_CONTENT_KEY'))) {
            $where = ('typeid in(' . session(config('app.USER_CONTENT_KEY')) . ') and ');
        }
        $where .= '1=1';
        $count = [];
        $op = '';
        $oplist = $type->whereRaw($where)->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select()->toArray();
        foreach ($oplist as $k => $v) {
            if ($v['fid'] == 0) {
                $count[$k] = '';
            } else {
                $count[$k] = '';
                for ($i = 0; $i < count(explode('-', $v['bpath'])) * 2; $i++) {
                    $count[$k] .= '&nbsp;';
                }
            }
            $op .= "<option value=\"{$v['typeid']}\">{$count[$k]}|-{$v['typename']}</option>";
        }
        $this->assign('op2', $op);
    }

    //文章模块 快速跳转栏目option
    private function jumpop()
    {
        $type = M('type');
        $where = '';
        if (!session(config('app.ADMIN_AUTH_KEY')) && session(config('app.USER_CONTENT_KEY'))) {
            $where = ('typeid in(' . session(config('app.USER_CONTENT_KEY')) . ') and ');
        }
        $where .= '1=1';
        $oplist = $type->whereRaw($where)->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select()->toArray();
        //echo $type->getLastSql();
        $count = [];
        $op = '';
        foreach ($oplist as $k => $v) {
            $check = '';
            if ($this->request->param('typeid')) {
                if ($v['typeid'] == intval($this->request->param('typeid'))) {
                    $check = 'selected="selected"';
                }
            } else if (cookie('curr_typeid') != '') {
                if ($v['typeid'] == intval(cookie('curr_typeid'))) {
                    $check = 'selected="selected"';
                }
            }
            if ($v['fid'] == 0) {
                $count[$k] = '';
            } else {
                $count[$k] = '';
                for ($i = 0; $i < count(explode('-', $v['bpath'])) * 2; $i++) {
                    $count[$k] .= '&nbsp;';
                }
            }
            $op .= "<option value=\"" . U('Article/index?typeid=' . $v['typeid']) . "\" $check>{$count[$k]}|-{$v['typename']}(栏目ID:{$v['typeid']})</option>";
        }
        $this->assign('op', $op);
    }

    //文章模块 添加-栏目option
    private function addop()
    {
        $type = M('type');
        //获取栏目option
        $where = '';
        if (!session(config('app.ADMIN_AUTH_KEY')) && session(config('app.USER_CONTENT_KEY'))) {
            $where = ('typeid in(' . session(config('app.USER_CONTENT_KEY')) . ') and ');
        }
        $where .= '1=1';
        $count = [];
        $option = '';
        $list = $type->whereRaw($where)->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select()->toArray();
        foreach ($list as $k => $v) {
            $check = '';
            if ($this->request->param('typeid')) {
                if ($v['typeid'] == intval($this->request->param('typeid'))) {
                    $check = 'selected="selected"';
                }
            }

            if ($v['fid'] == 0) {
                $count[$k] = '';
            } else {
                $count[$k] = '';
                for ($i = 0; $i < count(explode('-', $v['bpath'])) * 2; $i++) {
                    $count[$k] .= '&nbsp;';
                }
            }
            $option .= "<option value=\"{$v['typeid']}\" $check>{$count[$k]}|-{$v['typename']}</option>";
        }
        $this->assign('option', $option);
    }

    //文章模块-编辑-栏目option
    private function editop()
    {
        $aid = $this->request->param('aid');
        if(!$aid){return;}
        $article = M('article');
        $a = $article->whereRaw('aid=' . $aid)->field('typeid')->find();
        $type = M('type');
        $where = '';
        if (!session(config('app.ADMIN_AUTH_KEY')) && session(config('app.USER_CONTENT_KEY'))) {
            $where = ('typeid in(' . session(config('app.USER_CONTENT_KEY')) . ') and ');
        }
        $count = [];
        $where .= '1=1';
        $option = '';
        $list = $type->removeOption()->whereRaw($where)->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select()->toArray();
        foreach ($list as $k => $v) {
            if ($v['fid'] == 0) {
                $count[$k] = '';
            } else {
                $count[$k] = '';
                for ($i = 0; $i < count(explode('-', $v['bpath'])) * 2; $i++) {
                    $count[$k] .= '&nbsp;';
                }
            }

            if ($v['typeid'] == $a['typeid']) {
                $option .= "<option value=\"{$v['typeid']}\" selected>{$count[$k]}|-{$v['typename']}</option>";
            } else {
                $option .= "<option value=\"{$v['typeid']}\">{$count[$k]}|-{$v['typename']}</option>";
            }
        }
        $this->assign('option', $option);
    }

    //投票模块:for add()
    private function vote($vid)
    {
        $vote = M('vote');
        $vo = $vote->whereRaw('status=1')->field('id,title')->select()->toArray();
        if ($vid == 0) {
            $votehtml = '<option value=\"0\" selected>不投票</option>';
        } else {
            $votehtml = '<option value=\"0\">不投票</option>';
        }
        foreach ($vo as $k => $v) {
            if ($v['id'] == $vid) {
                $votehtml .= "<option value=\"{$vo['id']}\" selected>{$v['title']}</option>";
            } else {
                $votehtml .= "<option value=\"{$vo['id']}\">{$v['title']}</option>";
            }
        }
        $this->assign('votehtml', $votehtml);
        unset($votehtml);
    }

    public function search()
    {
        $article = D('ArticleView');
        $map['title'] = array('like', '%' . $_POST['keywords'] . '%');
        if($_POST['keywords']){
            $this->_log_operation('搜索文章包含:'.$_POST['keywords']);
        }
        $count = $article->where($map)->orderRaw('addtime desc')->count();
        $p = new Page($count, 20);
        $list = $article->removeOption()->where($map)->orderRaw('addtime desc')->limit($p->firstRow,$p->listRows)->select()->toArray();
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '篇文章');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>");
        $this->assign('page', $p->show());
        $this->assign('list', $list);
        $this->moveop();//文章编辑option
        $this->jumpop();//快速跳转option
        return $this->display('index');
    }
}

?>