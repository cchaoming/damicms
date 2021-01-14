<?php
/***********************************************************
[大米CMS] (C)2011 - 2011 damicms.com

@function 留言模块

@Filename GuestbookAction.class.php $

@Author 追影 QQ:279197963 $

@Date 2011-11-18 09:01:26 $
 *************************************************************/
namespace app\home\controller;
use think\facade\Session;
use until\Page;

class Guestbook extends Base
{
    public function index()
    {
        $this->assign('title','访客留言');
        //用于ajaxjs的根路径变量
        $url='';
        $this->assign('url',$url);
        return $this->display($this->template.'/guestbook.html');
    }

    public function update()
    {
        //输出gb2312码,ajax默认转的是utf-8
        header("Content-type: text/html; charset=utf-8");
        if(!isset($_POST['author']) or !isset($_POST['content']))
        {
            alert('非法操作!',3);
        }
        //读取数据库和缓存
        $pl = M('guestbook',true);
        $config = config('basic');
        //相关判断
        if(Session::has('posttime'))
        {
            $temp = Session::get('posttime') + $config['postovertime'];
            if(time() < $temp)
            {
                echo "请不要连续发布!";
                exit;
            }
        }
        //准备工作
        if($config['bookoff'] == 0) $data['status'] = 0;
        //先解密js的escape
        $data['author'] = htmlspecialchars(unescape($_POST['author']));
        $data['content'] = htmlspecialchars(trim(unescape($_POST['content'])));
        $data['title'] = htmlspecialchars(trim(unescape($_POST['title'])));
        $data['tel'] = htmlspecialchars(trim(unescape($_POST['tel'])));
        $data['ip'] = remove_xss(htmlentities(get_client_ip()));
        $data['addtime'] = date('Y-m-d H:i:s');
        //处理数据
        if($pl->save($data))
        {
            Session::set('posttime', time());
            if($config['bookoff'] == 0)
            {
                echo '发布成功,留言需要管理员审核!';
                exit;
            }
            else
            {
                echo '发布成功!';
                exit;
            }
        }
        else
        {
            echo '发布失败!';
            exit;
        }
    }

    public function show()
    {
        //读取数据库
        $pl = M('guestbook');
        //相关判断
        $data[] = ['status','=',1];
        $list = $pl->where($data)->select()->toArray();

        if(!$list)
        {
            $content = $this->display($this->template.'/guestbook_nopl.html');
            $this->ajaxReturn($content,null,null,'xml');
            exit;
        }
        //分页处理
        $count = $pl->removeOption()->where($data)->count();
        $this->assign('allnum',$count);
        //每10条分页
        $pagenum = 5;
        $p = new Page($count,$pagenum,'','page');
        //总页数
        $pages = ceil($count / $pagenum);
        $plist = $pl->removeOption()->where($data)->orderRaw('addtime desc')->limit($p->firstRow,$pagenum)->select()->toArray();
        //echo $pl->getLastSql();
        foreach($plist as $k=>$v)
        {
            if(!empty($v['recontent']))
            {
                $v['recontent'] = '<font color=red><b>管理员回复：'.$v['recontent'].'</b></font>';
            }
            $pp[$k]=$v;
            $pp[$k]['num'] = $k + 1 + (intval($_GET['page']) - 1) * $pagenum;
        }
        //当前页
        $this->assign('nowpage',intval($_GET['page']));
        //总页数
        $this->assign('pages',$pages);
        //最后一条记录数
        $this->assign('lastnum',$pagenum);
        $this->assign('list',$pp);
        $content = $this->display($this->template.'/guestbook_pl.html');
        $this->ajaxReturn($content,null,null,'xml');
    }
}
?>