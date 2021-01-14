<?php
/***********************************************************
[大米CMS] (C)2011 - 2011 damicms.com

@function 前台心情 Action

@Filename MoodAction.class.php $

@Author 追影 QQ:279197963 $

@Date 2011-11-17 22:01:55 $
 *************************************************************/
namespace app\home\controller;

use app\BaseController;

class Mood extends BaseController
{
    Public function _empty()
    {
        alert('方法不存在',3);
    }

    public function index()
    {
        inject_check($_GET['aid']);
        $mood = M('mood',true);
        $list = $mood->where('aid='.intval($_GET['aid']))->find();
        if($list)
        {
            echo "{$list['mood1']},{$list['mood2']},{$list['mood3']},{$list['mood4']},{$list['mood5']},{$list['mood6']},{$list['mood7']},{$list['mood8']}";
        }
        else
        {
            $data['aid'] = intval($_GET['aid']);
            $mood->removeOption()->save($data);
            echo "0,0,0,0,0,0,0,0";
        }
    }

    public function update()
    {
        $type = $_GET['type'];
        $mood = M('mood',true);
        $list = $mood->where('aid','=',(int)$_GET['aid'])->find();
        if($list)
        {
            switch($type)
            {
                case 'mood1':
                    $mood->inc('mood1',1)->update();
                    break;
                case 'mood2':
                    $mood->inc('mood2',1)->update();
                    break;
                case 'mood3':
                    $mood->inc('mood3',1)->update();
                    break;
                case 'mood4':
                    $mood->inc('mood4',1)->update();
                    break;
                case 'mood5':
                    $mood->inc('mood5',1)->update();
                    break;
                case 'mood6':
                    $mood->inc('mood6',1)->update();
                    break;
                case 'mood7':
                    $mood->inc('mood7',1)->update();
                    break;
                case 'mood8':
                    $mood->inc('mood87',1)->update();
                    break;
            }
            echo "{$list['mood1']},{$list['mood2']},{$list['mood3']},{$list['mood4']},{$list['mood5']},{$list['mood6']},{$list['mood7']},{$list['mood8']}";
        }
        else
        {
            echo '0,0,0,0,0,0,0,0';
        }
    }
}
?>