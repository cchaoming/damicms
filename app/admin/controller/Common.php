<?php

/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 系统RBAC认证
 *
 * @Filename CommonAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-17 14:57:09 $
 *************************************************************/

namespace app\admin\controller;

use app\BaseController;
use think\facade\Db;
use think\Response;
use think\exception\HttpResponseException;
use until\RBAC;

class Common extends BaseController
{
    protected function initialize()
    {
//模拟关闭magic_quotes_gpc 不关闭有时视频用不起
        if (get_magic_quotes_gpc()) {
            $_GET = stripslashesRecursive($_GET);
            $_POST = stripslashesRecursive($_POST);
            $_COOKIE = stripslashesRecursive($_COOKIE);
        }
//先检查cookie
        if (!session('?username') || !session('?admin_group_id')) {
            throw new HttpResponseException(Response::create(url(config('app.USER_AUTH_GATEWAY')),'redirect',302));
        }

        // 用户权限检查
        if (config('app.USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', config('app.NOT_AUTH_MODULE')))) {
            if (!RBAC::AccessDecision()) {
                //检查认证识别号
                if (!session(config('app.USER_AUTH_KEY'))) {
                    //跳转到认证网关
                    throw new HttpResponseException(Response::create(url(config('app.USER_AUTH_GATEWAY')),'redirect',302));
                }
                // 没有权限 抛出错误
                if (config('app.RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    redirect(config('app.RBAC_ERROR_PAGE'));
                } else {
                    if (config('app.GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', url(config('app.USER_AUTH_GATEWAY')));
                    }
                    // 提示错误信息
                    $this->error('您无访问权限', null, '', 0);
                }
            } else {
                //记录后台访问操作
                if (!in_array(MODULE_NAME, array('Publics', 'Article', 'Admin', 'Type'))) {
                    $model_info = M('node')->whereRaw("level=2 and name='" . MODULE_NAME . "'")->find();
                    $last_log = M('log')->whereRaw("username='" . session('username') . "'")->orderRaw('addtime desc')->find();
                    if ($model_info['id']) {
                        $action_info = M('node')->whereRaw("level=3 and name='" . ACTION_NAME . "' and pid=" . $model_info['id'])->find();
                        if ($action_info && $last_log['operate'] != '操作' . $action_info['title']) {
                            $this->_log_operation('操作' . $action_info['title']);
                        } else if ($last_log['operate'] != '操作' . $model_info['title']) {
                            $this->_log_operation('操作' . $model_info['title']);
                        }
                    }
                }
            }
        }
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

    //返回所有类型
    public function type_tree(){
        $type = M('type');
        $where ='';
        if(!session(config('app.ADMIN_AUTH_KEY')) && session(config('app.USER_CONTENT_KEY'))){
            $where = ('typeid in('.session(config('app.USER_CONTENT_KEY')).') and ');
        }
        $where .= 'islink=0';
        $oplist = $type->removeOption()->whereRaw($where)->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select()->toArray();
        //echo $type->getLastSql();
        $this->assign('type_tree',$oplist);
    }

}

?>