<?php
/***********************************************************
[大米CMS] (C)2011 - 2011 damicms.com

@function 认证模块

@Filename PublicAction.class.php $

@Author 追影 QQ:279197963 $

@Date 2011-11-17 15:14:43 $
 *************************************************************/
namespace app\admin\controller;
use app\BaseController;
use think\captcha\facade\Captcha;
use think\facade\Cookie;
use think\facade\Db;
use until\RBAC;

class Publics extends BaseController
{
    public function index()
    {
        return $this->redirect('Index/index');
    }

    public function login()
    {
        if($this->check_lock()){
            return $this->display('lock');
        }else{
            return $this->display('login');
        }
    }

    public function check_lock(){
        $ip = get_client_ip();
        $lock_info = M('admin_lock')->whereRaw("ip='{$ip}' and expire_time>".time())->find();
        if($lock_info){
            return  true;
        }
        return false;
    }

    function checkLogin()
    {
        if($this->check_lock()){
            return $this->display('lock');
        }

        if(!session('?admin_err_number')){
            session('admin_err_number',0);
        }

        if(empty($_POST['username']))
        {
            $this->error("帐号错误");
        }
        elseif (empty($_POST['password']))
        {
            $this->error("密码必须!");
        }
        elseif (empty($_POST['verify']))
        {
            $this->error('验证码必须!');
        }
        if (!captcha_check($this->request->post('verify'))) {
            $this->error('验证码错误!',url('Publics/login'));
        }
        //生成认证条件
        $map            =   array();
        // 支持使用绑定帐号登录
        $username = inject_check($_POST['username']);
        $map[]	= ['username','=',$username];
        $map[]	= ['status','>',0];
        $authInfo = RBAC::authenticate($map);
        //使用用户名、密码和状态的方式进行认证
        if(false === $authInfo)
        {
            session('admin_err_number',(int)session('admin_err_number')+1);
            $this->error('帐号不存在!');
        }
        if(empty($authInfo))
        {
            session('admin_err_number',(int)session('admin_err_number')+1);
            $this->error('帐号不存在或已禁用!');
        }
        $pwdinfo = strcmp($authInfo['password'],md5('wk'.trim($_POST['password']).'cms'));
        $ip = get_client_ip();
        if($pwdinfo <> 0)
        {
            session('admin_err_number',(int)session('admin_err_number')+1);
            $this->_log_operation('账号'.$username.'尝试登录,密码错误','失败');
            if(session('admin_err_number') >=5){
                //锁定该IP
                $lock = array();
                $lock['ip'] = $ip;
                $lock['expire_time'] = time() + 15*60;
                M('adminLock',true)->save($lock);
                $this->_log_operation('IP:'.$lock['ip'].'已被锁定，限制登录','限制');
            }
            $this->error('密码错误!');
        }
        M('admin_lock')->whereRaw("ip='{$ip}'")->delete();
        session('admin_err_number', 0);
        $this->_log_operation($username.'登录成功');
        session(config('app.USER_AUTH_KEY'),	$authInfo['id']);
        session('username',	(string)$_POST['username']);
        $role = M('role_admin');
        $authInfo['role_id'] = $role->whereRaw('user_id='.(int)$authInfo['id'])->value('role_id');
        if($authInfo['role_id'] == '1')
        {
            session(config('app.ADMIN_AUTH_KEY'),true);
        }
        session('admin_group_id',$authInfo['role_id']);
        session('admin_group',get_field('role','id='.$authInfo['role_id'],'name'));
        //保存登录信息
        $admin	=	M('admin',true);
        $ip		=	get_client_ip();
        $time	=	time();
        $data = array();
        $data['lastlogintime']	=	$time;
        $data['lastloginip']	=	$ip;
        $admin->whereRaw('id='.(int)$authInfo['id'])->save($data);
        RBAC::saveAccessList();
        return $this->index();
    }

    function loginout()
    {
        if(session(config('app.USER_AUTH_KEY')))
        {
            $this->_log_operation(session('username').'退出登录');
            session(config('app.USER_AUTH_KEY'),null);
            session(null);
            $this->success('登出成功!',U('Publics/login'));
        }
        $this->assign("jumpUrl",U('Public/slogin'));
        $this->error('已经登出!');
    }

    public function verify()
    {
        return Captcha::create();
    }

    function _log_operation($log, $result = '成功')
    {
        if (!session(config('app.USER_AUTH_KEY'))) {
            $username = '游客';
            $usergroup = '';
        } else {
            if (session('username') && session('admin_group')) {
                $username = session('username');
                $usergroup = session('admin_group');
            } else {
                $sql = 'SELECT role_admin.role_id,role.`name` as groupname,admin.username FROM ' . config('app.RBAC_USER_TABLE') . ' as role_admin LEFT JOIN dami_admin as admin ON role_admin.user_id = admin.id left JOIN ' . config('app.RBAC_ROLE_TABLE') . ' as role ON  role.id = role_admin.role_id WHERE admin.id = ' . session(config('app.USER_AUTH_KEY')) . ' and role.`status` = 1';
                //echo $sql;
                $rs = Db::query($sql);
                if ($rs) {
                    $usergroup = $rs[0]['groupname'];
                    session('admin_group', $rs[0]['groupname']);
                    $username = $rs[0]['username'];
                    session('username', $rs[0]['username']);
                }
            }
        }
        $ip = get_client_ip();
        $data = array();
        $data['username'] = $username;
        $data['usergroup'] = $usergroup;
        $data['operate'] = $log;
        $data['result'] = $result;
        $data['ip'] = $ip;
        $data['addtime'] = time();
        M('log', true)->save($data);
        unset($data);
    }
}
?>