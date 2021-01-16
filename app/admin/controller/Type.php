<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 栏目管理模块
 *
 * @Filename TypeAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-23 10:33:18 $
 *************************************************************/

namespace app\admin\controller;
class Type extends Common
{
    public function index()
    {
        $type = M('type');
        $article = M('article');
        $list = $type->field("typeid,typename,ismenu,isindex,islink,isuser,drank,irank,fid,concat(path,'-',typeid) as bpath")->group('bpath,drank')->select()->toArray();
        //echo $type->getLastSql();
        foreach ($list as $k => $v) {
            $list[$k]['is_last'] = $type->removeOption()->whereRaw('fid=' . $v['typeid'])->count();
            $list[$k]['count'] = count(explode('-', $v['bpath']));
            $list[$k]['total'] = $article->removeOption()->whereRaw('typeid=' . $v['typeid'])->count();
            $str = '';
            if ($v['fid'] <> 0) {
                for ($i = 0; $i < $list[$k]['count'] * 2; $i++) {
                    $str .= '&nbsp;';
                }
                $str .= '|-';
            }
            $list[$k]['space'] = $str;
        }
        $this->assign('list', $list);
        unset($type, $article, $list);
        return $this->display('index');
    }

    //字段显示控制
    function show_field()
    {
        $typeid = (int)$this->request->param('typeid', 0);
        if ($typeid == 0) {
            $this->error('错误的分类编号');
            exit();
        }
        //字段默认显示
        $arr = array(
            array('txt' => '标题', 'show' => 1, 'field_name' => 'title'),
            array('txt' => '关键字', 'show' => 1, 'field_name' => 'keywords'),
            array('txt' => '描述', 'show' => 1, 'field_name' => 'description'),
            array('txt' => '作者', 'show' => 1, 'field_name' => 'author'),
            array('txt' => '来源', 'show' => 1, 'field_name' => 'copyfrom'),
            array('txt' => '浏览次数', 'show' => 1, 'field_name' => 'hits'),
            array('txt' => '分类', 'show' => 1, 'field_name' => 'typeid'),
            array('txt' => '转向链接', 'show' => 1, 'field_name' => 'linkurl'),
            array('txt' => '缩略图', 'show' => 1, 'field_name' => 'imgurl'),
            array('txt' => '文章摘要', 'show' => 1, 'field_name' => 'note'),
            array('txt' => '附件上传', 'show' => 1, 'field_name' => ''),
            array('txt' => '内容', 'show' => 1, 'field_name' => 'content'),
            array('txt' => '发布时间', 'show' => 1, 'field_name' => 'addtime'),
            array('txt' => '附加选项', 'show' => 1, 'field_name' => '固顶:istop 推荐:ishot 幻灯:isflash'),
            array('txt' => '自动分页字数', 'show' => 1, 'field_name' => 'pagenum'),
            array('txt' => '本文显示投票', 'show' => 1, 'field_name' => 'voteid')
        );
        $temp = M('type')->whereRaw('typeid=' . $typeid)->find();

        if ($temp) {
            $this->assign('field_name', $temp['typename']);
            $myarr = explode('|', $temp['show_fields']);
            //dump($myarr);
            if (count($myarr) > 10) {
                for ($i = 0; $i < count($myarr); $i++) {
                    $arr[$i]['show'] = $myarr[$i];
                }
            }
        }
        $this->assign('list', $arr);
        //加载扩展字段不想用JOIN个人认为效率不高
        $list_extend = M('extend_fieldes')->orderRaw('orders asc')->select()->toArray();
        foreach ($list_extend as $k => $v) {
            $is = (int)get_field('extend_show','typeid=' . $typeid . ' and field_id=' . $v['field_id'],'is_show');
            $is_show = $is == 1 ? 1 : 0;
            $list_extend[$k]['is_show'] = $is_show;
        }
        $this->assign('list_extend', $list_extend);
        $this->assign('typeid', $typeid);
        return $this->display();
    }

    //模板控制
    function manage_moban()
    {
        $typeid = (int)$this->request->param('typeid');
        if ($typeid == 0) {
            $this->error('错误的分类编号');
            exit();
        }
        $temp = M('type')->whereRaw('typeid=' . $typeid)->find();
        $this->assign('typeid', $typeid);
        $this->assign('model_path', $temp);
        return $this->display();
    }

    //保存字段显示控制
    function doshow_field()
    {
        $typeid = (int)$this->request->param('typeid', 0);
        $nset = (int)$this->request->param('nset', 0);
        $outids = htmlspecialchars($this->request->param('outids'));
        if ($typeid == 0) {
            $this->error('错误的分类编号');
            exit();
        }
        $str = join('|', array_slice($_POST, 1, 16));
        $data = array();
        $data['typeid'] = $typeid;
        $data['show_fields'] = $str;
        $dao = M('type', true);
        $temp = $dao->where('typeid=' . $typeid)->save($data);
        //保存扩展字段显示
        $list_extend = M('extend_fieldes')->orderRaw('orders asc')->select()->toArray();
        foreach ($list_extend as $k => $v) {
            //不是第一次设置
            $dao = M('extend_show')->whereRaw('typeid=' . $typeid . ' and field_id=' . $v['field_id'])->find();
            if ($dao) {
                M('extend_show')->whereRaw('typeid=' . $typeid . ' and field_id=' . $v['field_id'])->save(['is_show' => intval($_POST['field_' . $v['field_id']])]);
            } else {
                //第一次设置
                $arr['typeid'] = $typeid;
                $arr['field_id'] = $v['field_id'];
                $arr['is_show'] = intval($_POST['field_' . $v['field_id']]);
                $arr['orders'] = $v['orders'];
                M('extendShow', true)->save($arr);
            }
        }
        //下一级保存该设置
        if ($nset == 1) {
            $where = '';
            if ($outids != '') {
                $where .= ('typeid not in(' . str_replace('|', ',', $outids) . ') and');
            }
            $ids = get_children($typeid);
            $where .= "typeid in($ids)";
            M('type')->whereRaw($where)->save(['show_fields' => $str]);
            $childids = M('type')->whereRaw($where)->select();
            foreach ($childids as $key => $value) {
                foreach ($list_extend as $k => $v) {
                    $dao = M('extend_show')->whereRaw('typeid=' . $value['typeid'] . ' and field_id=' . $v['field_id'])->find();
                    if ($dao) {
                        M('extend_show')->whereRaw('typeid=' . $value['typeid'] . ' and field_id=' . $v['field_id'])->save(['is_show' => intval($_POST['field_' . $v['field_id']])]);
                    } else {
                        $arr['typeid'] = $value['typeid'];
                        $arr['field_id'] = $v['field_id'];
                        $arr['is_show'] = intval($_POST['field_' . $v['field_id']]);
                        $arr['orders'] = $v['orders'];
                        M('extendShow', true)->save($arr);
                    }
                    //echo M('extend_show')->getLastSql();
                }
            }
        }
        $this->success('保存字段显示成功');
    }

    //保存模板
    function domanage_moban()
    {
        $typeid = (int)$this->request->param('typeid', 0);
        $nset = (int)$this->request->param('nset', 0);
        $outids = htmlspecialchars($this->request->param('outids'));
        if ($typeid == 0) {
            $this->error('错误的分类编号');
            exit();
        }
        $data = array();
        $data['typeid'] = $typeid;
        $data['list_path'] = $_REQUEST['list_path'];
        $data['page_path'] = $_REQUEST['page_path'];
        //dump($data);
        $dao = M('type');
        $temp = $dao->whereRaw('typeid=' . $typeid)->save($data);
        //保存下一级配置
        if ($nset == 1) {
            $where = '';
            if ($outids != '') {
                $where .= ('typeid not in(' . str_replace('|', ',', $outids) . ') and');
            }
            $ids = get_children($typeid);
            $where .= "typeid in($ids)";
            M('type')->whereRaw($where)->save(['list_path' => $_REQUEST['list_path'], 'page_path' => $_REQUEST['page_path']]);
        }
        $this->success('保存模板路劲成功');
    }

    public function add()
    {
        $type = M('type');
        $list = $type->whereRaw("islink=0")->field("typeid,typename,ismenu,isindex,islink,isuser,drank,irank,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select()->toArray();
        foreach ($list as $key => $value) {
            $list[$key]['count'] = count(explode('-', $value['bpath']));
        }
        $this->assign('list', $list);
        unset($type, $list);
        return $this->display('add');
    }

    public function doadd()
    {
        if (empty($_POST['typename'])) {
            $this->error('栏目名称不能为空!');
        }
        //构造上级的字段显示和模型
        $fid = 0;
        if (isset($_POST['fid']) && intval($_POST['fid']) > 0) {
            $fid = intval($_POST['fid']);
            $temp = M('type')->where('typeid=' . $fid)->find();
            if ($temp) {
                $_POST['show_fields'] = $temp['show_fields'];
                $_POST['list_path'] = $temp['list_path'];
                $_POST['page_path'] = $temp['page_path'];
            }
        }
        $type = D('Type');
        if ($type->save($_POST)) {
            $id = $type->typeid;
            if ($id) {
                //更新path
                $type->tclm($id, $fid, false);
                //构造扩展字段
                $list = M('extend_show')->whereRaw('typeid=' . $fid)->select()->toArray();
                foreach ($list as $k => $v) {
                    $data = $v;
                    unset($data['id']);
                    $data['typeid'] = $id;
                    M('extendShow', true)->save($data);
                }
                $this->_log_operation('添加栏目' . $_POST['typename']);
                $this->assign("waitSecond", 30);
                $this->assign("jumpUrl", U('Type/index'));
                $this->success('操作成功! 您可以<a href="' . U('Type/add', array('fid' => $fid)) . '" style="color:green">继续添加</a>&nbsp;&nbsp;<a href="' . U('Type/index') . '" style="color:red">返回分类列表</a>');
            }
            $this->_log_operation('添加栏目', '失败');
            $this->error('操作失败!');
        }
        $this->_log_operation('添加栏目' . $type->getError(), '失败');
        $this->error($type->getError());
    }

    public function edit()
    {
        $typeid = (int)$this->request->param('typeid', 0);
        $type = M('type');
        $list = $type->whereRaw('typeid=' . $typeid)->find();
        //获取栏目option
        $olist = $type->removeOption()->whereRaw("islink=0")->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select()->toArray();
        $option = '';
        foreach ($olist as $k => $v) {
            $count[$k] = '';
            $ban = '';
            if ($v['fid'] <> 0) {
                for ($i = 0; $i < count(explode('-', $v['bpath'])) * 2; $i++) {
                    $count[$k] .= '&nbsp;';
                }
            }

            if ($v['typeid'] == $this->request->param('typeid')) {
                $ban = " disabled='disabled'";
            }

            if ($v['typeid'] == $list['fid']) {
                $option .= "<option value=\"{$v['typeid']}\" selected{$ban}>{$count[$k]}|-{$v['typename']}</option>";
            } else {
                $option .= "<option value=\"{$v['typeid']}\"{$ban}>{$count[$k]}|-{$v['typename']}</option>";
            }
        }

        if ($list['fid'] == 0) {
            $option .= '<option value="0" selected>做为顶级分类</option>';
        } else {
            $option .= '<option value="0">做为顶级分类</option>';
        }
        $this->assign('option', $option);
        $this->assign('list', $list);
        unset($list, $type);
        return $this->display('edit');
    }

//执行编辑
    public function doedit()
    {
        $typeid = (int)$this->request->param('typeid', 0);
        if ($typeid && $this->request->isPost()) {
            $fid = (int)$this->request->param('fid', 0);
            if (empty($_POST['typename'])) {
                $this->error('栏目名称不能为空!');
            }
            $type = D('Type');
            $info = $type->whereRaw('typeid=' . $typeid)->find();
            if ($info && $info->save($_POST)) {
                //更新path
                $type->tclm($typeid, $fid, true);
                $this->_log_operation('修改栏目' . $_POST['typename']);
                $this->assign("jumpUrl", U('Type/index'));
                $this->success('操作成功!');

            }
        }
        $this->error('操作成功,什么也未改变!');
    }

    //删除栏目&执行删除
    public function del()
    {
        $typeid = (int)$this->request->param('typeid', 0);
        if (!$typeid) {
            $this->error('参数错误!');
        }
        $type = M('type');
        $article = M('article');
        if ($type->whereRaw('fid=' . $typeid)->select()->toArray()) {
            $this->assign("jumpUrl", U('Type/index'));
            $this->error('请先删除子栏目!');
        }
        if ($article->whereRaw('typeid=' . $typeid)->select()->toArray()) {
            $this->assign("jumpUrl", U('Type/index'));
            $this->error('请先清空栏目下文章!');
        }
        $type->removeOption()->whereRaw('typeid=' . $typeid)->delete();
        M('extend_show')->whereRaw('typeid=' . $typeid)->delete();
        $this->_log_operation('删除栏目ID：' . $typeid);
        $this->assign("jumpUrl", U('Type/index'));
        $this->success('删除成功!');
    }

    //ajax扩展后台菜单
    function ajax_menuid()
    {
        $typeid = (int)$this->request->param('typeid', 0);
        $t = M('extend_menu')->whereRaw('typeid=' . $typeid)->find();
        if ($t) {
            $this->ajaxReturn($t, 'ok', 1);
        } else {
            $this->ajaxReturn(array(), 'err', 0);
        }
    }

    //ajax保存扩展菜单
    function ajax_domenu()
    {
        $typeid = (int)$this->request->param('typeid', 0);
        if ($typeid == 0) {
            exit('参数错误');
        }
        $t = M('extend_menu')->whereRaw('typeid=' . $typeid)->find();
        if ($t) {
            if (intval($this->request->param('enable')) == 2) {
                M('extend_menu')->whereRaw('typeid=' . $typeid)->delete();
            } else {
                M('extendMenu', true)->whereRaw('typeid=' . $typeid)->save(array_map("unescape", $_REQUEST));
            }
        } else {
            M('extendMenu', true)->save(array_map("unescape", $_REQUEST));
        }
    }

    public function status()
    {
        $typeid = (int)$this->request->param('typeid', 0);
        $a = M('type');
        $s = $this->request->param('s');
        if (!$typeid || !$s) {
            $this->error('参数非法');
        }
        $s = explode("-", $s);
        if (!is_array($s) || count($s) < 2) {
            $this->error('参数非法');
        }
        if ($s[1] == 0) {
            $a->whereRaw('typeid=' . $typeid)->save([$s[0] => 1]);
        } elseif ($s[1] == 1) {
            $a->whereRaw('typeid=' . $typeid)->save([$s[0] => 0]);
        } else {
            $this->error('非法操作');
        }
        return $this->redirect('Type/index');
    }
}

?>