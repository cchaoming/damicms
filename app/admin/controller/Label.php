<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 标签管理
 *
 * @Filename LabelAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-23 10:25:56 $
 *************************************************************/

namespace app\admin\controller;
class Label extends Common
{
    public function index()
    {
        $label = M('label');
        $list = $label->orderRaw('addtime desc')->select()->toArray();
        $this->assign('list', $list);
        return $this->display('index');
    }

//添加标签
    public function add()
    {
        return $this->display('add');
    }

//执行添加
    public function doadd()
    {
        if ($this->request->isPost()) {
            $label = M('label', true);
            $data['title'] = $_POST['title'];
            //使用stripslashes 反转义,防止服务器开启自动转义
            $data['content'] = stripslashes($_POST['content']);
            $data['addtime'] = date('Y-m-d H:i:s');
            if ($label->save($data)) {
                $this->assign("jumpUrl", U('Label/index'));
                $this->success('操作成功!');
            }
        }
        $this->error('操作失败!');
    }

    public function edit()
    {
        $id = (int)$this->request->param('id');
        $label = M('label');
        $list = $label->whereRaw('id=' . $id)->find();
        $this->assign('list', $list);
        return $this->display();
    }

    public function doedit()
    {
        $label = M('label', true);
        $id = (int)$this->request->param('id');
        $data['title'] = $_POST['title'];
        $data['content'] = stripslashes($_POST['content']);
        $data['addtime'] = date('Y-m-d H:i:s');
        $info = $label->find($id);
        if ($info && $info->save($data)) {
            $this->assign("jumpUrl", U('Label/index'));
            $this->success('操作成功!');
        }
        $this->error('操作失败!');
    }

    public function del()
    {
        $id = (int)$this->request->param('id');
        $type = M('label');
        if ($type->where('id=' . $id)->delete()) {
            $this->assign("jumpUrl", U('Label/index'));
            $this->success('操作成功!');
        }
        $this->error('操作失败!');
    }

    public function status()
    {
        $status = M('label');
        $go = (int)$this->request->param('status');
        $id = (int)$this->request->param('id');
        if ($go == 0) {
            $status->where('id=' . $id)->save(['status' => 1]);
        } elseif ($go == 1) {
            $status->where('id=' . $id)->save(['status' => 0]);
        } else {
            $this->error('非法操作!');
        }
        return $this->redirect('Label/index');
    }
}

?>