<?php
/***********************************************************
[大米CMS] (C)2011 - 2011 damicms.com

@function 前台评论 Action

@Filename PlAction.class.php $

@Author 追影 QQ:279197963 $

@Date 2011-11-17 21:30:58 $
 *************************************************************/
namespace  app\home\controller;
use app\BaseController;
use think\exception\HttpResponseException;
use think\facade\Session;
use think\Response;

class Pl extends BaseController
{

    public function update()
    {
        //输出utf-8码,ajax默认转的是utf-8
        header("Content-type: text/html; charset=utf-8");
        if(!isset($_POST['aid']) or !isset($_POST['author']) or !isset($_POST['content']))
        {
            $this->error('非法操作!');
        }
        //读取数据库和缓存
        $pl = M('pl');
        $config = config('basic');
        $data['ip'] = htmlentities(get_client_ip());
        //先解密js的escape
        $data['author'] = htmlspecialchars(unescape($_POST['author']));
        //使用stripslashes 反转义,防止服务器开启自动转义
        $data['content'] = htmlspecialchars(trim(stripslashes(unescape($_POST['content']))));
        $data['ptime'] = date('Y-m-d H:i:s');
        $data['aid'] = intval($_POST['aid']);
        if(session('dami_uid')){
            $data['pl_uid'] = intval(session('dami_uid'));
        }

        if(Session::has('pltime'))
        {
            $temp=Session::get('pltime') + $config['postovertime'];
            if(time() < $temp)
            {
                echo "请不要连续发布!";
                exit;
            }
        }
        if($config['pingoff'] == 0) $data['status'] = 0;
        if($pl->save($data))
        {
            Session::set('pltime', time());
            if($config['pingoff'] == 0)
            {
                echo "发布成功,评论需要管理员审核!";
                exit;
            }
            else
            {
                echo "发布成功!";
                exit;
            }
        }
        else
        {
            echo "发布失败!";
            exit;
        }
    }

    public function index()
    {
        inject_check($_GET['aid']);
        $page = (int)$this->request->param('page',1);
        if($page<1){exit();}
        $pl = M('pl');
        $data[] = ['status','=',1];
        $data[] = ['aid','=',intval($_GET['aid'])];
        $list = $pl->where($data)->select()->toArray();
        if(!$list)
        {
            return $this->display(TMPL_PATH.TMPL_NAME.'/pl_nopl.html');
            exit;
        }
        $count = $pl->removeOption()->where($data)->count();
        $this->assign('allnum',$count);
        $pagenum = 6;//每六条分页
        $pages = ceil($count / $pagenum);//总页数
        $prepage = ($page-1) * $pagenum;
        $tempnum = $pagenum * $_GET['page'];
        $lastnum = ($tempnum < $count) ? $tempnum : $count;
        $plist = $pl->removeOption()->where($data)->orderRaw('ptime asc')->limit($prepage,$pagenum)->select()->toArray();
        foreach($plist as $k=>$v)
        {
            if(!empty($v['recontent']))
            {
                $v['recontent'] = '<font color=red><b>管理员回复：'.$v['recontent'].'</b></font>';
            }
            $pp[$k] = $v;
            $pp[$k]['num']= $k + 1 + (intval($_GET['page'])-1) * $pagenum;
        }
        //封装变量
        $this->assign('nowpage',$page);//当前页
        $this->assign('pages',$pages);//总页数
        $this->assign('aid',intval($_GET['aid']));//文章aid
        $this->assign('lastnum',$lastnum);//最后一条记录数
        $this->assign('list',$pp);
        //模板输出
        $result = $this->display(TMPL_PATH.TMPL_NAME.'/pl_pl.html');
        $this->ajaxReturn($result,null,null,'xml');
    }
}
?>