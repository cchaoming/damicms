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
class AdminAction extends CommonAction
{
    public function index()
    {
        $admin = M('admin');
        if ($_SESSION[C('ADMIN_AUTH_KEY')]) {
            $list = $admin->field('id,lastlogintime,lastloginip,username,status')->select();
        } else {
            $list = $admin->where('id=' . $_SESSION['authId'])->field('id,lastlogintime,lastloginip,username,status')->select();
        }
        foreach ($list as $k => $v) {
            $role_id = M('role_admin')->where('user_id=' . $v['id'])->getField('role_id');
            if (intval($role_id) > 0) {
                $list[$k]['role_name'] = M('role')->where('id=' . $role_id)->getField('name');
            }
        }
        $this->assign('list', $list);
        $this->display('index');
    }

    //添加管理员
    public function add()
    {
        if (!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            $this->_log_operation('非超级管理员,无权添加用户', '失败');
            $this->error('无权操作!');
        }
        $this->display();
    }

    //组管理
    function groupindex()
    {
        if (!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            $this->_log_operation('非超级管理员,无权管理组', '失败');
            $this->error('无权操作!');
        }
        $list = M('role')->select();
        $this->assign('list', $list);
        $this->display();
    }

    //节点首页
    function nodeindex()
    {
        if (!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            $this->_log_operation('非超级管理员,无权管理权限节点', '失败');
            $this->error('无权操作!');
        }
        $list = M('node')->select();
        $this->assign('list', $list);
        $this->display();
    }

    //添加管理组
    function groupadd()
    {
        self::type_tree();
        $this->nodeadd();
    }

    //修改管理组
    function editgroup()
    {
        $role_id = (int)$_GET['id'];
        $info = M('role')->where('id=' . $role_id)->find();
        $rinfo = M('access')->where('role_id=' . $role_id)->select();
        $this->assign('info', $info);
        $this->assign('rinfo', $rinfo);
        self::groupadd();
    }

    //添加节点
    function nodeadd()
    {
        if(!$_SESSION[C('ADMIN_AUTH_KEY')]){
            $this->error('无权操作!');
        }
        self::node_tree();
        $this->display();
    }

    //编辑节点
    function editnode()
    {
        $info = M('node')->where('id=' . intval($_GET['id']))->find();
        $this->assign('info', $info);
        self::nodeadd();
    }

    //保存添加节点
    function donodeadd()
    {
        if(!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            $this->_log_operation('非超级管理员,无权添加权限', '失败');
            $this->error('无权操作!');
        }
        M('node')->add($_POST);
        $this->assign("jumpUrl", U('Admin/nodeindex'));
        $this->success('操作成功!');
    }

    //保存添加组
    function dogroupadd()
    {
        if(!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            $this->_log_operation('非超级管理员,无权添加管理组', '失败');
            $this->error('无权操作!');
        }
        //保存组名
        if (!$_POST['manageids']) {
            $this->error('请选择管理栏目权限和管理内容权限!');
        }
        $data = $_POST;
        $data['typeids'] = implode(',', $_POST['typeids']);
        $role_id = M('role')->add($data);
        if ($role_id <= 0) {
            $this->error('添加管理组失败!');
        }
        $mids = $_POST['manageids'];
        //保存组栏目权限数据
        for ($i = 0; $i < count($mids); $i++) {
            $temp = array();
            $t = M('node')->where('status=1 and id=' . intval($mids[$i]))->find();
            if ($t) {
                $temp['role_id'] = $role_id;
                $temp['node_id'] = intval($mids[$i]);
                $temp['level'] = $t['level'];
                $temp['pid'] = $t['pid'];
                M('access')->add($temp);
                unset($temp);
            }
        }
        $this->_log_operation('管理组添加成功');
        $this->success('管理组添加成功!');
    }

    //保存修改组
    function dogroupedit()
    {
        if(!$_SESSION[C('ADMIN_AUTH_KEY')]){
            $this->_log_operation('无权修改管理组', '失败');
            $this->error('无权操作!');
        }
        //保存组名
        if (!$_POST['manageids']) {
            $this->error('请选择管理栏目权限和管理内容权限!');
        }
        $data = $_POST;
        $role_id = (int)$_POST['id'];
        $data['typeids'] = implode(',', $_POST['typeids']);
        M('role')->where('id=' . $role_id)->save($data);
        $mids = $_POST['manageids'];
        M('access')->where('role_id=' . $role_id)->delete();
        //保存组栏目权限数据
        for ($i = 0; $i < count($mids); $i++) {
            $temp = array();
            $t = M('node')->where('status=1 and id=' . intval($mids[$i]))->find();
            if ($t) {
                $temp['role_id'] = $role_id;
                $temp['node_id'] = intval($mids[$i]);
                $temp['level'] = $t['level'];
                $temp['pid'] = $t['pid'];
                M('access')->add($temp);
                unset($temp);
            }
        }
        $this->_log_operation('修改管理组成功');
        $this->success('修改管理组成功!');
    }

    //修改节点
    function doeditnode()
    {
        if(!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            $this->error('无权操作!');
        }
        M('node')->where('id=' . intval($_POST['id']))->save($_POST);
        $this->_log_operation('修改管理权限成功');
        $this->assign("jumpUrl", U('Admin/nodeindex'));
        $this->success('操作成功!');
    }

    //删除节点
    function delnode()
    {
        $id = (int)$_GET['id'];
        $temp = self::get_childID_list($id);
        $temp[] = $id;
        M('node')->where('id in(' . join(',', $temp) . ')')->delete();
        $this->_log_operation('删除管理权限成功');
        $this->success('删除成功!');
    }

    //删除管理组
    function delgroup()
    {
        if(!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            $this->_log_operation('无权删除管理权限', '失败');
            $this->error('无权操作!');
        }
        $role_id = (int)$_GET['id'];
        M('role')->where('id=' . $role_id)->delete();
        M('access')->where('role_id=' . $role_id)->delete();
        $this->_log_operation('删除管理组成功');
        $this->success('删除成功!');
    }

    //递归获得某分类下级
    private function get_childID_list($id)
    {
        $dao = M('node');
        $ret = array();
        $child = array();
        $list = $dao->where('pid=' . $id)->select();
        foreach ($list as $k => $v) {
            $child = get_childID_list($v['id']);
        }
        return array_merge($ret, $child);
    }

    //栏目权限树
    private function node_tree()
    {
        $node_tree = M('node')->where('status=1')->select();
        $this->assign('node_tree', $node_tree);
    }

    //内容权限树
    private function type_tree()
    {
        $type_tree = M('type')->where('islink=0')->select();
        $this->assign('type_tree', $type_tree);
    }

    //执行添加
    public function doadd()
    {
        if(!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            $this->_log_operation('无权添加管理用户','失败');
            $this->error('无权操作!');
        }
        $admin = M('admin');
        if (C('TOKEN_ON') && !$admin->autoCheckToken($_POST)) {
            $this->error(L('_TOKEN_ERROR_'));
        }
        $data['username'] = trim($_POST['username']);
        if ($admin->where('username=\'' . $_POST['username'] . '\'')->find()) {
            $this->error('用户名已存在!');
        }
        if (empty($_POST['password'])) {
            $this->error('密码不能为空!');
        }
        if($this->checkPasswordRule($_POST['password'])==false){
            $this->error("密码必须包含大写字母、小写字母、数字，且长度8~20位");
        }
        $data['lastlogintime'] = time();
        $data['lastloginip'] = get_client_ip();
        $data['is_client'] = intval($_POST['is_client']);
        $data['password'] = md5('wk' . trim($_POST['password']) . 'cms');
        $role = M('role_admin');
        if ($admin->add($data)) {
            $map['user_id'] = $admin->where('username=\'' . $_POST['username'] . '\'')->getField('id');
            $map['role_id'] = (int)$_POST['role_id'];
            $role->add($map);
            $this->_log_operation('添加管理用户:' . $_POST['username'] . '成功');
            $this->assign("jumpUrl", U('Admin/index'));
            $this->success('操作成功!密码为:' . $_POST['password']);
        }
        $this->error('操作失败!');
    }

    //修改管理员
    public function edit()
    {

        if (!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            //只能操作自己
            if ($_SESSION['authId'] <> $_GET['id']) {
                $this->_log_operation('无权修改管理用户','失败');
                $this->error('无权操作!');
            }
            $role_id = M('role_admin')->where('user_id=' . $_SESSION['authId'])->getField('role_id');
            $role_list = M('role')->where('status=1 and id=' . $role_id)->select();
        } else {
            $role_list = M('role')->where('status=1')->select();
        }

        $this->assign('role_list', $role_list);
        $admin = M('admin');
        $list = $admin->where('id=' . $_GET['id'])->find();
        $cur_roleid = M('role_admin')->where('user_id=' . $_GET['id'])->getField('role_id');
        $this->assign('list', $list);
        $this->assign('role_id', $cur_roleid);
        $this->display();
    }

    public function doedit()
    {
        if (!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            if ($_SESSION['authId'] <> $_POST['id']) {
                $this->_log_operation('无权修改管理用户ID'.$_POST['id'],'失败');
                $this->error('无权操作!');
            }
        }
        if (empty($_POST['username'])) {
            $this->error('用户名不能为空!');
        }
        M('role_admin')->where('user_id=' . $_POST['id'])->setField('role_id', $_POST['role_id']);
        $admin = M('admin');
        if (C('TOKEN_ON') && !$admin->autoCheckToken($_POST)) {
            $this->error(L('_TOKEN_ERROR_'));
        }
        $data['username'] = trim($_POST['username']);
        $data['id'] = $_POST['id'];
        $dopwd = '';
        if (!empty($_POST['password'])) {
            if($this->checkPasswordRule($_POST['password'])==false){
                $this->_log_operation('修改管理用户:' . $data['username'] .'密码必须包含大写字母、小写字母、数字','失败');
                $this->error("密码必须包含大写字母、小写字母、数字，且长度8~20位");
            }
            $dopwd = '改密码';
            $data['password'] = md5('wk' . trim($_POST['password']) . 'cms');
        }
        $admin->save($data);
        $this->_log_operation('修改管理用户:' . $data['username'] . $dopwd . '成功');
        $this->assign("jumpUrl", U('Admin/index'));
        $this->success('操作成功!');

    }

    public function del()
    {
        if (!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            if ($_SESSION['authId'] <> $_GET['id']) {
                $this->_log_operation('无权删除管理用户ID'.$_GET['id'],'失败');
                $this->error("无权操作!");
            }
        }
        if ($_GET['id'] == $_SESSION['authId']) {
            $this->_log_operation('不能删除自己','失败');
            $this->error('不能删除自己!');
        }
        $type = M('admin');
        M('role_admin')->where('user_id=' . intval($_GET['id']))->delete();
        if ($type->where('id=' . $_GET['id'])->delete()) {
            $this->_log_operation('删除管理员ID：' . intval($_GET['id']) . '成功');
            $this->assign("jumpUrl", U('Admin/index'));
            $this->success('操作成功!');
        } else {
            $this->_log_operation('删除管理员ID：' . intval($_GET['id']) . '失败', '失败');
            $this->error('操作失败!');
        }
    }

    public function checkPasswordRule($password){
        if(!preg_match('/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9]{8,20}/',$password)){
            return false;
        }
        return true;
    }

    public function status()
    {
        if (!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            if ($_SESSION['authId'] <> $_GET['id']) {
                $this->_log_operation('无权启用或禁用管理员');
                $this->error('无权操作!');
            }
        }

        if ($_GET['id'] == $_SESSION['authId']) {

            $this->error('不能禁用自己!');
        }

        $a = M('admin');

        if ($_GET['status'] == 0) {
            $this->_log_operation('启用管理员ID：' . intval($_GET['id']) . '成功');
            $a->where('id=' . $_GET['id'])->setField('status', 1);
        } elseif ($_GET['status'] == 1) {
            $this->_log_operation('禁用管理员ID：' . intval($_GET['id']) . '成功');
            $a->where('id=' . $_GET['id'])->setField('status', 0);
        } else {
            $this->error('非法操作');
        }
        $this->redirect('index');
    }
}

?>