<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 关键字管理
 *
 * @Filename KeyAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-23 10:20:24 $
 *************************************************************/

namespace app\admin\controller;

use until\Page;

class Key extends Common
{
    public function index()
    {
        $key = M('key');
        $count = $key->count();
        $p = new Page($count, 20);
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '条记录');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
        $this->assign('page', $p->show());
        $list = $key->limit($p->firstRow, $p->listRows)->select()->toArray();
        $this->assign('list', $list);
        return $this->display('index');
    }

    public function add()
    {
        return $this->display('add');
    }

    public function doadd()
    {
        if ($this->request->isPost()) {
            $key = M('key', true);
            if ($key->save($this->request->post())) {
                $this->assign("jumpUrl", U('Key/index'));
                $this->success('操作成功!');
            }
        }
        $this->error('操作失败!');
    }

    public function edit()
    {
        $id = (int)$this->request->param('id');
        $key = M('key');
        $list = $key->whereRaw('id=' . $id)->find();
        $this->assign('list', $list);
        return $this->display();
    }

    public function doedit()
    {
        if ($this->request->isPost()) {
            $id = (int)$this->request->param('id');
            $key = M('key',true);
            $info = $key->find($id);
            if ($info && $info->save($this->request->post())) {
                $this->assign("jumpUrl", U('Key/index'));
                $this->success('操作成功!');
            }
        }
        $this->error('操作失败!');
    }

    public function del()
    {
        $type = M('key');
        $id = (int)$this->request->param('id');
        if ($id) {
            $type->whereRaw('id=' . $id)->delete();
            $this->assign("jumpUrl", U('Key/index'));
            $this->success('操作成功!');

        }
        $this->error('操作失败!');

    }

    public function delall()
    {
        $id = $_REQUEST['id'];  //获取id
        $id = is_array($id) ? $id : explode(',', $id);
        if (!$id) {
            $this->assign("jumpUrl", U('Key/index'));
            $this->error('请勾选记录!');
        }
        $map[] = ['id', 'in', $id];
        $key = M('key');
        if ($_REQUEST['Del'] == '编辑') {
            for ($i = 0; $i < count($_REQUEST['keyid']); $i++) {
                $data['url'] = $_REQUEST['url'][$i];
                $key->removeOption()->whereRaw('id=' . (int)$_REQUEST['keyid'][$i])->save($data);
            }
            $this->assign("jumpUrl", U('Key/index'));
            $this->success('操作成功!');
        }

        if (!$id) {
            $this->error('请勾选记录!');
        }

        if ($_REQUEST['Del'] == '删除') {
            if ($key->where($map)->delete()) {
                $this->assign("jumpUrl", U('Key/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }
    }

    public function search()
    {
        $keyword = (string)$this->request->param('keywords');
        if (!$keyword) {
            $this->error('操作失败!');
        }
        $map[] = ['title', 'like', '%' . $keyword . '%'];
        $key = M('key');
        $count = $key->where($map)->count();
        $p = new Page($count, 20);
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '条记录');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
        $this->assign('page', $p->show());
        if ($this->request->isPost()) {
            $p->parameter .= 'keywords=' . $keyword;
        }
        $list = $key->limit($p->firstRow,$p->listRows)->select()->toArray();
        $this->assign('list', $list);
        return $this->display('index');
    }
}

?>