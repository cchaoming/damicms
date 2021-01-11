<?php
/***********************************************************
[大米CMS] (C)2011 - 2011 damicms.com

@function 前台投票	Action

@Filename VoteAction.class.php $

@Author 追影 QQ:279197963 $

@Date 2011-11-18 11:58:00 $
 *************************************************************/
namespace app\home\controller;
class Vote extends Base
{

    public function index()
    {
        if(empty($_GET['id']))
        {
            alert('非法操作!',3);
        }
        inject_check($_GET['id']);

        //读取数据库
        $vote = M('vote');
        $vo = $vote->whereRaw('id='.intval($_GET['id']))->find();

        //业务处理
        if(!$vo)
        {
            $this->error('投票不存在或已删除!');
        }

        $over='';
        if(!cptime($vo['overtime'],date('Y-m-d H:i:s')))
        {
            $over='[已结束]';
        }

        $total = 0;
        $strs = explode(PHP_EOL,trim($vo['vote']));

        for($i = 0;$i < count($strs);$i++)
        {
            $s = explode('=',$strs[$i]);
            $data[$i]['num'] = $s[1];
            $data[$i]['title'] = $s[0];
            $total += $s[1];
        }

        foreach($data as $k=>$v)
        {
            $data[$k]['percent'] = round($v['num'] / $total * 100 + 0);
        }
        //封装变量
        $this->assign("vote",$data);
        $this->assign("over",$over);
        $this->assign("votetitle",$vo['title']);
        $this->assign("starttime",$vo['starttime']);
        $this->assign("overtime",$vo['overtime']);
        $this->assign("total",$total);
        //模板输出
        return $this->display(TMPL_PATH.cookie('think_template').'/vote.html');
    }



    public function update()
    {
        if(empty($_POST))
        {
            alert('请选择投票项!',1);
        }
        inject_check($_POST['id']);
        if(Cookie::is_set('vote'.$_POST['id']))
        {
            alert('您已投过票了!',1);
        }
        //读取数据库
        $vote=M('vote');
        $vo=$vote->whereRaw('id='.intval($_POST['id']))->field('vote,overtime,starttime,stype')->find();
        $strs = explode(PHP_EOL,trim($vo['vote']));
        //业务处理
        if(!$vo)
        {
            alert('投票不存在!',3);
        }

        if($vo['overtime'] !='' && cptime(date('Y-m-d H:i:s'),$vo['overtime']))
        {
            alert('投票已结束!',U('votes/'.$_POST['id']));
        }

        if(!cptime(date('Y-m-d H:i:s'),$vo['starttime']))
        {
            alert('投票没有开始!',U('votes/'.$_POST['id']));
        }

        $data['vote'] = $vo['vote'];

        if($vo['stype'] == 0)
        {
            $_POST['vote'] = array($_POST['vote']);
        }
        foreach($_POST['vote'] as $v)
        {
            $v = str_replace(PHP_EOL,"",$v);
            if(in_array($v,$strs)){
                $s = explode("=",$v);
                if(count($s)==2 && is_numeric($s[1])){
                    $data['vote'] = str_replace($v,$s[0]."=".(intval($s[1]) + 1),$data['vote']);
                }
            }
        }
        if($vote->removeOption()->where('id='.intval($_POST['id']))->save($data))
        {
            Cookie::set('vote'.$_POST['id'],'1',365 * 60 * 60 * 24);
            alert('投票成功!',U('votes/'.$_POST['id']));
        }
        alert("操作失败!",U('votes/'.$_POST['id']));
    }
}
?>