<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 留言管理
 *
 * @Filename GuestbookAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-17 15:03:17 $
 *************************************************************/

namespace app\admin\controller;

use until\Page;

class Guestbook extends Common
{
    public function index()
    {
        $guestbook = M('guestbook');
        $where = '1=1';
        if ($this->request->param('status')) {
            $where .= ' and status=' . (int)$this->request->param('status');
        }
        $count = $guestbook->whereRaw($where)->orderRaw('addtime desc')->count();
        $p = new Page($count, 20);
        //这里会保留查询条件
        $list = $guestbook->limit($p->firstRow, $p->listRows)->select()->toArray();
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '条评论');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条评论 20条/每页</span></li>");
        $this->assign('page', $p->show());
        $this->assign('list', $list);
        return $this->display('index');
    }

    public function edit()
    {
        $id = (int)$this->request->param('id');
        $type = M('guestbook');
        $list = $type->where('id=' . $id)->find();
        $this->assign('list', $list);
        return $this->display('edit');
    }

    public function doedit()
    {
        $id = (int)$this->request->param('id');
        $guestbook = M('guestbook',true);
        //使用stripslashes 反转义,防止服务器开启自动转义
        $data['content'] = stripslashes($_POST['content']);
        $data['recontent'] = stripslashes($_POST['recontent']);
        $info = $guestbook->find($id);
        if ($info && $info->save($data)) {
            $this->assign("jumpUrl", U('Guestbook/index'));
            $this->success('操作成功!');
        }
        $this->error('操作失败!');
    }

    public function del()
    {
        $type = M('guestbook');
        $id = (int)$this->request->param('id');
        if ($id) {
            $type->whereRaw('id=' . $id)->delete();
            $this->assign("jumpUrl", U('Guestbook/index'));
            $this->success('操作成功!');
        }
        $this->error('操作失败!');
    }

    public function status()
    {
        $a = M('guestbook');
        $id = (int)$this->request->param('id');
        $status = (int)$this->request->param('status');
        if ($status == 0) {
            $a->where('id=' . $id)->save(['status'=>1]);
        } elseif ($status == 1) {
            $a->where('id=' . $id)->save(['status'=>0]);
        } else {
            $this->error('非法操作!');
        }
        return $this->redirect('Guestbook/index');
    }

    public function delall()
    {
        $id = $_REQUEST['id'];  //获取文章id
        if (!$id) {
            $this->error('请勾选记录!');
        }
        $id = is_array($id) ? $id:explode(',',$id);
        $map[] = ['id','in', $id];
        $guestbook = M('guestbook');
        if ($_REQUEST['Del'] == '删除') {
            if ($guestbook->where($map)->delete()) {
                $this->assign("jumpUrl", U('Guestbook/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }

        if ($_REQUEST['Del'] == '批量未审') {
            $data['status'] = 0;
            if ($guestbook->where($map)->save($data) !== false) {
                $this->assign("jumpUrl", U('Guestbook/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }

        if ($_REQUEST['Del'] == '批量审核') {
            $data['status'] = 1;
            if ($guestbook->where($map)->save($data) !== false) {
                $this->assign("jumpUrl", U('Guestbook/index'));
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }
    }

    public function search()
    {

        $keywords = (string)$this->request->param('keywords');
        if(!$keywords){$this->error('参数错误!');}
        $guestbook = M('guestbook');
        $map[] = ['content','like', '%' . $keywords . '%'];
        $count = $guestbook->where($map)->order('addtime desc')->count();
        $p = new Page($count, 20);
        $list = $guestbook->limit($p->firstRow,$p->listRows)->select()->toArray();
        if($this->request->isPost()){
            $p->parameter .= 'keywords='.$keywords;
        }
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '条评论');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条评论 20条/每页</span></li>");
        $this->assign('page', $p->show());
        $this->assign('list', $list);
        return $this->display('index');
    }
}

?>