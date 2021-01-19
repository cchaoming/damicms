<?php

/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 管理员管理
 *
 * @Filename AdminAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-23 10:38:17 $
 *************************************************************/

namespace app\admin\controller;
class Admin extends Common
{
    public function index()
    {
        $admin = M('admin');
        if (session(config('app.ADMIN_AUTH_KEY'))) {
            $list = $admin->field('id,lastlogintime,lastloginip,username,status')->select()->toArray();
        } else {
            $list = $admin->whereRaw('id=' . (int)session('authId'))->field('id,lastlogintime,lastloginip,username,status')->select()->toArray();
        }
        foreach ($list as $k => $v) {
            $role_id = M('role_admin')->whereRaw('user_id=' . $v['id'])->value('role_id');
            if (intval($role_id) > 0) {
                $list[$k]['role_name'] = M('role')->whereRaw('id=' . $role_id)->value('name');
            }
        }
        $this->assign('list', $list);
        return $this->display('index');
    }

    private function check_superadmin()
    {
        if (!session(config('app.ADMIN_AUTH_KEY'))) {
            $this->_log_operation('非超级管理员,无权管理权限节点', '失败');
            $this->error('无权操作!');
        }
    }

    //添加管理员
    public function add()
    {
        $this->check_superadmin();
        return $this->display();
    }


    //组管理
    function groupindex()
    {
        $this->check_superadmin();
        $list = M('role')->select()->toArray();
        $this->assign('list', $list);
        return $this->display();
    }

    //节点首页
    function nodeindex()
    {

        $list = M('node')->select()->toArray();
        $this->assign('list', $list);
        return $this->display();
    }

    //添加管理组
    function groupadd()
    {
        self::type_tree();
        return $this->nodeadd();
    }

    //修改管理组
    function editgroup()
    {
        $role_id = (int)$this->request->param('id');
        $info = M('role')->whereRaw('id=' . $role_id)->find();
        $rinfo = M('access')->whereRaw('role_id=' . $role_id)->select()->toArray();
        $this->assign('info', $info);
        $this->assign('rinfo', $rinfo);
        return self::groupadd();
    }

    //添加节点
    function nodeadd()
    {
        $this->check_superadmin();
        self::node_tree();
        return $this->display();
    }

    //编辑节点
    function editnode()
    {
        $id = (int)$this->request->param('id');
        $info = M('node')->whereRaw('id=' . intval($id))->find();
        $this->assign('info', $info);
        return self::nodeadd();
    }

    //保存添加节点
    function donodeadd()
    {
        $this->check_superadmin();
        if($this->request->isPost()){
        $fid = (int)$this->request->param('pid');
        $_POST['menu_pid'] = $fid;
        M('node', true)->save($_POST);
        $this->assign("jumpUrl", U('Admin/nodeindex'));
        $this->success('操作成功!');
        }
    }

    //保存添加组
    function dogroupadd()
    {
        $this->check_superadmin();
        if ($this->request->isPost()) {
            //保存组名
            if (!$_POST['manageids']) {
                $this->error('请选择管理栏目权限和管理内容权限!');
            }
            $data = $_POST;
            $data['typeids'] = implode(',', $_POST['typeids']);
            $dao = M('role', true);
            $dao->save($data);
            $role_id = $dao->id;
            if ($role_id <= 0) {
                $this->error('添加管理组失败!');
            }
            $mids = $_POST['manageids'];
            //保存组栏目权限数据
            for ($i = 0; $i < count($mids); $i++) {
                $temp = [];
                $t = M('node')->whereRaw('status=1 and id=' . intval($mids[$i]))->find();
                if ($t) {
                    $temp['role_id'] = $role_id;
                    $temp['node_id'] = intval($mids[$i]);
                    $temp['level'] = $t['level'];
                    $temp['pid'] = $t['pid'];
                    M('access')->save($temp);
                    unset($temp);
                }
            }
            $this->_log_operation('管理组添加成功');
            $this->success('管理组添加成功!');
        }
    }

    //保存修改组
    function dogroupedit()
    {
        $this->check_superadmin();
        if ($this->request->isPost()) {
            //保存组名
            if (!$_POST['manageids']) {
                $this->error('请选择管理栏目权限和管理内容权限!');
            }
            $data = $_POST;
            $role_id = (int)$_POST['id'];
            $data['typeids'] = implode(',', $_POST['typeids']);
            $info = M('role', true)->whereRaw('id=' . $role_id)->find();
            if ($info) {
                $info->save($data);
            }
            $mids = $_POST['manageids'];
            M('access')->whereRaw('role_id=' . $role_id)->delete();
            //保存组栏目权限数据
            for ($i = 0; $i < count($mids); $i++) {
                $temp = array();
                $t = M('node')->whereRaw('status=1 and id=' . intval($mids[$i]))->find();
                if ($t) {
                    $temp['role_id'] = $role_id;
                    $temp['node_id'] = intval($mids[$i]);
                    $temp['level'] = $t['level'];
                    $temp['pid'] = $t['pid'];
                    M('access')->save($temp);
                    unset($temp);
                }
            }
            $this->_log_operation('修改管理组成功');
            $this->success('修改管理组成功!');
        }
    }

    //修改节点
    function doeditnode()
    {
        if ($this->request->isPost()) {
            $id = $this->request->param('id');
            $this->check_superadmin();
            $info = M('node', true)->whereRaw('id=' . intval($id))->find();

            $_POST['menu_pid'] = $id = (int)$this->request->param('pid');
            if ($info) {
                $info->save($_POST);
            }
            $this->_log_operation('修改管理权限成功');
            $this->assign("jumpUrl", U('Admin/nodeindex'));
            $this->success('操作成功!');
        }
    }

    //删除节点
    function delnode()
    {
        $id = (int)$this->request->param('id');
        $temp = self::get_childID_list($id);
        $temp[] = $id;
        M('node')->whereRaw('id in(' . join(',', $temp) . ')')->delete();
        $this->_log_operation('删除管理权限成功');
        $this->success('删除成功!');
    }

    //删除管理组
    function delgroup()
    {
        $this->check_superadmin();
        $role_id = (int)$this->request->param('id');
        M('role')->whereRaw('id=' . $role_id)->delete();
        M('access')->whereRaw('role_id=' . $role_id)->delete();
        $this->_log_operation('删除管理组成功');
        $this->success('删除成功!');
    }

    //递归获得某分类下级
    private function get_childID_list($id)
    {
        $dao = M('node');
        $ret = array();
        $child = array();
        $list = $dao->whereRaw('pid=' . $id)->select()->toArray();
        foreach ($list as $k => $v) {
            $child = get_childID_list($v['id']);
        }
        return array_merge($ret, $child);
    }

    //栏目权限树
    private function node_tree()
    {
        $node_tree = M('node')->whereRaw('status=1')->select()->toArray();
        $this->assign('node_tree', $node_tree);
    }

    //执行添加
    public function doadd()
    {
        $this->check_superadmin();
        if ($this->request->isPost()) {
            $admin = M('admin',true);
            $this->verify_token();
            $data['username'] = trim($_POST['username']);
            $info = $admin->whereRaw('username=\'' . $_POST['username'] . '\'')->find();
            if ($info) {
                $this->error('用户名已存在!');
            }
            if (empty($_POST['password'])) {
                $this->error('密码不能为空!');
            }
            if ($this->checkPasswordRule($_POST['password']) == false) {
                $this->error("密码中不能含有空格长度8~20位");
            }
            $data['lastlogintime'] = time();
            $data['lastloginip'] = get_client_ip();
            $data['is_client'] = $this->request->param('is_client',0);
            $data['password'] = md5('wk' . trim($_POST['password']) . 'cms');
            $role = M('role_admin');
            if ($admin->save($data) !== false) {
                $map['user_id'] = get_field('admin', 'username=\'' . $_POST['username'] . '\'', 'id');
                $map['role_id'] = (int)$_POST['role_id'];
                $role->save($map);
                $this->_log_operation('添加管理用户:' . $_POST['username'] . '成功');
                $this->assign("jumpUrl", U('Admin/index'));
                $this->success('操作成功!密码为:' . $_POST['password']);
            }
        }
        $this->error('操作失败!');
    }

    //修改管理员
    public function edit()
    {

        $id = (int)$this->request->param('id');
        if (!session(config('app.ADMIN_AUTH_KEY'))) {
            //只能操作自己
            if (session('authId') != $id) {
                $this->_log_operation('无权修改管理用户', '失败');
                $this->error('无权操作!');
            }
            $role_id = M('role_admin')->whereRaw('user_id=' . (int)session('authId'))->value('role_id');
            $role_list = M('role')->whereRaw('status=1 and id=' . $role_id)->select()->toArray();
        } else {
            $role_list = M('role')->whereRaw('status=1')->select()->toArray();
        }
        $this->assign('role_list', $role_list);
        $list = M('admin')->whereRaw('id=' . $id)->find();
        $cur_roleid = M('role_admin')->whereRaw('user_id=' . $id)->value('role_id');
        $this->assign('list', $list);
        $this->assign('role_id', $cur_roleid);
        return $this->display();
    }

    public function doedit()
    {
        $this->check_superadmin();
        if (empty($_POST['username'])) {
            $this->error('用户名不能为空!');
        }
        $this->verify_token();
        M('role_admin')->whereRaw('user_id=' . $_POST['id'])->save(['role_id' => $_POST['role_id']]);
        $admin = M('admin', true);
        $data['username'] = trim($_POST['username']);
        $data['id'] = $_POST['id'];
        $dopwd = '';
        if (!empty($_POST['password'])) {
            if ($this->checkPasswordRule($_POST['password']) == false) {
                $this->_log_operation('修改管理用户:' . $data['username'] . '密码中不能含有空格长度8~20位', '失败');
                $this->error("密码中不能含有空格长度8~20位");
            }
            $dopwd = '改密码';
            $data['password'] = md5('wk' . trim($_POST['password']) . 'cms');
        }
        $info = $admin->find($_POST['id']);
        if($info){
        $info->save($data);
        $this->_log_operation('修改管理用户:' . $data['username'] . $dopwd . '成功');
        $this->assign("jumpUrl", U('Admin/index'));
        $this->success('操作成功!');
        }
    }

    public function del()
    {
        $id = (int)$this->request->param('id');
        if (!session(config('app.ADMIN_AUTH_KEY'))) {
            if (session('authId') != $id) {
                $this->_log_operation('无权删除管理用户ID' . $_GET['id'], '失败');
                $this->error("无权操作!");
            }
        }
        if ($id == session('authId')) {
            $this->_log_operation('不能删除自己', '失败');
            $this->error('不能删除自己!');
        }
        $type = M('admin');
        M('role_admin')->whereRaw('user_id=' . $id)->delete();
        $type->whereRaw('id=' . $id)->delete();
        $this->_log_operation('删除管理员ID：' . intval($id) . '成功');
        $this->assign("jumpUrl", U('Admin/index'));
        $this->success('操作成功!');
    }

    public function checkPasswordRule($password)
    {
        if (!preg_match('/((?!\s)\S){8,20}/', $password)) {
            return false;
        }
        return true;
    }

    public function status()
    {
        $this->check_superadmin();
        $id = (int)$this->request->param('id');
        $status = (int)$this->request->param('status');
        if ($id == session('authId')) {

            $this->error('不能禁用自己!');
        }
        $a = M('admin');

        if ($status == 0) {
            $this->_log_operation('启用管理员ID：' . intval($id) . '成功');
            $a->whereRaw('id=' . $id)->save(['status'=>1]);
        } elseif ($status == 1) {
            $this->_log_operation('禁用管理员ID：' . intval($id) . '成功');
            $a->whereRaw('id=' . $id)->save(['status'=> 0]);
        } else {
            $this->error('非法操作');
        }
        return $this->redirect('index');
    }
}

?>