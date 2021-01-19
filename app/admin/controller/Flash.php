<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 幻灯管理
 *
 * @Filename FlashAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-23 10:11:14 $
 *************************************************************/

namespace app\admin\controller;
class Flash extends Common
{
    public function index()
    {
        $flash = M('flash');
        $list = $flash->select()->toArray();
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
            //dump($_POST);
            $data['title'] = $_POST['title'];
            $data['pic'] = $_POST['pic'];
            $data['url'] = $_POST['url'];
            $data['status'] = $_POST['status'];
            $data['rank'] = $_POST['rank'];
            $flash = M('flash');
            if ($flash->save($data)) {
                $this->assign("jumpUrl", U('Flash/index'));
                $this->success('操作成功!');
            }
        }
        $this->error('操作失败!');
    }

    public function edit()
    {
        $id = (int)$this->request->param('id');
        if ($id) {
            $flash = M('flash');
            $list = $flash->whereRaw('id=' . $id)->find();
            $this->assign('list', $list);
            return $this->display('edit');
        }
    }

    public function doedit()
    {
        $id = (int)$this->request->param('id');
        if ($id) {
            $data['title'] = $_POST['title'];
            $data['pic'] = $_POST['pic'];
            $data['url'] = $_POST['url'];
            $data['status'] = $_POST['status'];
            $data['rank'] = $_POST['rank'];
            $flash = M('flash');
            if ($flash->whereRaw('id=' . $id)->save($data) !== false) {
                $this->assign("jumpUrl", U('Flash/index'));
                $this->success('操作成功!');
            }
        }
        $this->error('操作失败!');
    }

    public function del()
    {
        $id = (int)$this->request->param('id');
        $flash = M('flash');
        if ($id) {
            $flash->whereRaw('id=' . $id)->delete();
            $this->assign("jumpUrl", U('Flash/index'));
            $this->success('操作成功!');
        }
        $this->error('操作失败!');
    }

    public function status()
    {
        $status = M('flash');
        $id = (int)$this->request->param('id');
        $s = (int)$this->request->param('status');
        if ($id) {
            if ($s == 0) {
                $status->whereRaw('id=' . $id)->save(['status' => 1]);
            } elseif ($s == 1) {
                $status->whereRaw('id=' . $id)->save(['status' => 0]);
            } else {
                $this->error('非法操作!');
            }
        }
        return $this->redirect('Flash/index');
    }
}

?>