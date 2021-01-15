<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use think\facade\Db;

class Index extends Common
{
    //首页显示框架主体index
    public function index()
    {
        return $this->display('index');
    }

//首页显示框架内left页面
    public function left()
    {
        if (session(config('app.ADMIN_AUTH_KEY'))) {
            return $this->display('left_super');
        } else {
            $sql = "select role.id as role_id,node.* from dami_role as role,dami_role_admin as user,dami_access as access,dami_node as node where user.user_id=" . session(config('app.USER_AUTH_KEY')) . " and user.role_id=role.id and role.status=1 and role.id=access.role_id and access.node_id = node.id and node.ismenu=1 and menu_pid=37";
            $list = Db::query($sql);
            //栏目菜单一二级
            $own_access = array();
            $method = array();
            if (session('_ACCESS_LIST')) {
                foreach (session('_ACCESS_LIST') as $app => $module) {
                    if (is_array($module)) {
                        foreach ($module as $mod_name => $v) {
                            $own_access[] = $mod_name;
                            if (is_array($v) && $v) {
                                foreach ($v as $method_name => $vv)
                                    $method[$mod_name][] = $method_name;
                            }
                        }
                    }
                }
            }
            foreach ($list as $k => $v) {
                $submenu = M('node')->whereRaw('menu_pid=' . $v['id'])->select()->toArray();
                foreach ($submenu as $x => $y) {
                    if ($y['level'] == 2) {
                        $submenu[$x]['url'] = U($y['name'] . '/index');
                        if ($own_access && !in_array(strtoupper($y['name']), $own_access)) {
                            unset($submenu[$x]);
                        }
                    } else if ($y['level'] == 3) {
                        $model_name = M('node')->whereRaw('id=' . $y['pid'])->value('name');
                        $submenu[$x]['url'] = U($model_name . '/' . $y['name']);
                        if (($own_access && !in_array(strtoupper($model_name), $own_access)) || ($method && isset($method[strtoupper($model_name)]) && !in_array(strtoupper($y['name']), $method[strtoupper($model_name)]))) {
                            unset($submenu[$x]);
                        }

                    }
                }
                $list[$k]['submenu'] = $submenu;
            }
            //dump($list);
            $this->assign('admin_menu', $list);
            return $this->display('left_common');
        }
    }

//首页显示框架内head头部页面
    public function head()
    {
        return $this->display('head');
    }

//首页显示框架内bottom底部页面
    public function bottom()
    {
        return $this->display('bottom');
    }

//首页显示框架内center页面包含了left和main
    public function center()
    {
        return $this->display('center');
    }

//首页显示框架内右侧主页面
    public function main()
    {
        $count = array();
        $article = M('article');
        $type = M('type');
        $link = M('link');
        $hd = M('flash');
        $ping = M('pl');
        $guest = M('guestbook');
        $vip = M('vip_mess')->whereRaw('1=1')->find();
        if ($vip) {
            $url = config('app.SERVER_URL') . "/Public/check_webvip?vip_name=" . $vip['vip_name'] . "&vip_pwd=" . $vip['vip_pwd'];
            $res = json_decode(get_url_contents($url));
            if ($res->status == 0) {
                $vip_mess = '<font color="#FF0000">ㄨ您使用的是大米CMS未进行商业授权，请&nbsp;<a href="http://www.damicms.com/Public/donate">尽快购买</a>&nbsp;<a href="__APP__/Apk/vip_config">设置授权码</a></font>';
            } else {
                $vip_mess = '<font color="#006600">√ 恭喜你，您已获得大米CMS商业使用授权，更多技术参考&nbsp;<a href="http://www.damicms.com/">大米CMS官网</a>！</font>';
            }
        } else {
            $vip_mess = '<font color="#FF0000">ㄨ您使用的是大米CMS未进行商业授权，请&nbsp;<a href="http://www.damicms.com/Public/donate">尽快购买</a>&nbsp;<a href="__APP__/Apk/vip_config">设置授权码</a></font>';
        }
        $this->assign('vip_mess', $vip_mess);
        //文章总数
        $count['article'] = $article->removeOption()->count();
        //未审核文章总数
        $count['narticle'] = $article->removeOption()->whereRaw('status=0')->count();
        //留言总数
        $count['guestbook'] = $guest->count();
        //未审核留言总数
        $count['nguestbook'] = $guest->removeOption()->where('status=0')->count();
        //栏目总数
        $count['type'] = $type->count();
        //链接总数
        $count['link'] = $link->count();
        //幻灯总数
        $count['hd'] = $hd->count();
        //评论总数
        $count['ping'] = $ping->count();
        //未审核评论
        $count['nping'] = $ping->removeOption()->whereRaw('status=0')->count();
        $this->assign('count', $count);
        unset($article, $type, $link, $hd, $ping, $guest);
        $info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . '秒',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            '服务器域名/IP' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            'register_globals' => get_cfg_var("register_globals") == "1" ? "ON" : "OFF",
            'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime()) ? 'YES' : 'NO',
        );
        $this->assign('info', $info);
        return $this->display('main');
    }

//底部版权公共页
    public function copy()
    {
        return $this->display('copy');
    }
}
