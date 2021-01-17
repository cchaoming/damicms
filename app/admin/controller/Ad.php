<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 广告管理
 *
 * @Filename AdAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-23 09:59:26 $
 *************************************************************/

namespace app\admin\controller;

use until\Page;

class Ad extends Common
{
    public function index()
    {

        $ad = M('ad');
        $count = $ad->count();
        !is_null($this->request->param('type')) ? $map[] = ['type', '=', (int)$this->request->param('type')] : $map[] = ['type', '<>', 10];
        !is_null($this->request->param('status')) ? $map[] = ['status', '=', (int)$this->request->param('status')] : $map[''] = ['status', '<>', 2];
        $count = $ad->where($map)->count();
        $p = new Page($count, 20);
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '条记录');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
        $this->assign('page', $p->show());
        $list = $ad->removeOption()->where($map)->orderRaw('addtime desc')->limit($p->firstRow, $p->listRows)->select()->toArray();
        $this->assign('list', $list);
        return $this->display();
    }

    public function add()
    {
        return $this->display('add');
    }

    public function edit()
    {
        $id = (int)$this->request->param('id');
        $type = M('ad');
        $list = $type->whereRaw('id=' . $id)->find();
        $this->assign('list', $list);
        return $this->display('edit');
    }

    public function doedit()
    {
        if ($this->request->isPost()) {
            $id = (int)$this->request->param('id');
            $ad = M('ad', true);
            $data['title'] = $_POST['title'];
            $data['type'] = $_POST['type'];
            $data['description'] = $_POST['description'];
            //使用stripslashes 反转义,防止服务器开启自动转义
            $data['content'] = stripslashes($_POST['content']);
            $data['addtime'] = date('Y-m-d H:i:s');
            $info = $ad->find($id);
            if ($info->save($data)) {
                $this->assign("jumpUrl", U('Ad/index'));
                $this->success('操作成功!');
            }
        }
        $this->error('操作失败!');
    }

    public function doadd()
    {
        if ($this->request->isPost()) {
            $ad = M('ad', true);
            $data['title'] = $_POST['title'];
            $data['type'] = $_POST['type'];
            $data['description'] = $_POST['description'];
            $data['content'] = stripslashes($_POST['content']);
            $data['addtime'] = date('Y-m-d H:i:s');
            if ($ad->save($data)) {
                $this->assign("jumpUrl", U('Ad/index'));
                $this->success('操作成功!');
            }
        }
        $this->error('操作失败!');
    }

    public function del()
    {
        $id = (int)$this->request->param('id');
        if ($id) {
            $type = M('ad');
            if ($type->where('id=' . $id)->delete()) {
                $this->assign("jumpUrl", U('Ad/index'));
                $this->success('操作成功!');
            }
        }
        $this->error('操作失败!');
    }

    public function status()
    {
        $id = (int)$this->request->param('id');
        $status = (int)$this->request->param('status');
        $a = M('ad');
        if ($status == 0) {
            $a->where('id=' . $id)->save(['status'=>1]);
        } else {
            $a->where('id=' . $id)->save(['status'=>0]);
        }
        return $this->redirect('Ad/index');
    }


    public function delall()
    {
        $id = $this->request->param('id');
        $ids = implode(',', $id);//批量获取id
        $id = is_array($id) ? $ids : $id;
        $map['id'] = array('in', $id);
        if (!$id) {
            $this->error('请勾选广告!');
        }
        $ad = M('ad');
        if ($this->request->param('Del') == '显示') {
            $data['status'] = 1;
            if ($ad->where($map)->save($data) !== false) {
                $this->assign("jumpUrl", U('Ad/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }

        if ($this->request->param('Del') == '隐藏') {
            $data['status'] = 0;
            if ($ad->where($map)->save($data)!== false) {
                $this->assign("jumpUrl", U('Ad/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }
    }
}

?>