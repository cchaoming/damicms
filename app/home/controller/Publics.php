<?php
declare (strict_types=1);

namespace app\home\controller;
use until\Cart;
use until\RSS;

class Publics extends Base
{
    function single()
    {
        $sinfo = M('article')->whereRaw('aid=' . intval($_GET['aid']))->find();
        if ($sinfo) {
            $this->assign('title', $sinfo['title']);
            $this->assign('sinfo', $sinfo);
        }
        return $this->display();
    }

//RSS订阅
    function rss()
    {
        $myRss = new RSS("大米CMS", config('app.SERVER_URL'), "大米CMS");
        $list = M('article')->whereRaw('1=1')->select()->toArray();
        foreach ($list as $k => $v) {
            $myRss->AddItem($v['title'], 'http://' . $_SERVER['SERVER_NAME'] . url('articles', $v['aid']), $v['addtime']);
        }
        $myRss->BuildRSS();
        $myRss->SaveToFile('./feed.rss');
        $myRss->getFile('./feed.rss');
    }

//自动更新描述
    function up_desc()
    {
        $list = M('article')->whereRaw("description=''")->select()->toArray();
        foreach ($list as $k => $v) {
            M('article')->removeOption()->whereRaw('aid=' . $v['aid'])->save(['description'=>msubstr(strip_tags($v['content']), 0, 100)]);
        }
        $this->ajaxReturn('成功', '', 1,'html');
    }
//ajax加入购物车
    function ajax_insert_cart()
    {
        if (empty($_REQUEST['id']) || empty($_REQUEST['qty']) || empty($_REQUEST['price']) || empty($_REQUEST['name']) || !is_numeric($_REQUEST['price']) || !is_numeric($_REQUEST['qty']) || !is_numeric($_REQUEST['id']) || intval($_REQUEST['qty']) <= 0) {
            $this->ajaxReturn('参数错误', '失败', 0);
            exit();
        }
        $items = array(
            array(
                'id' =>(int)$_REQUEST['id'],
                'qty' => (int)$_REQUEST['qty'],
                'price' => $_REQUEST['price'],
                'name' => $_REQUEST['name'],
                'option' => array('gid'=> intval($_REQUEST['id']),'gtype' => (string)$_REQUEST['gtype'], 'pic' => (string)$_REQUEST['pic']),
            ),
        );
        $cart = new Cart();
        $cart->insert($items);
        $this->ajaxReturn($cart->contents(), '成功', 1);
    }

//ajax购物车物品列表
    function ajax_cart_list()
    {
        $cart = new Cart();
        $this->ajaxReturn($cart->contents(), '成功', 1);
    }

//将物品从购物车中删除
    function ajax_del_cart()
    {
        $id = intval($_REQUEST['id']);
        $cart = new Cart();
        $arr = array(
            'rowid' => md5(strval($id)),
            'qty' => 0//清楚该物品只要设置为0即可
        );
        $cart->update($arr);
    }
}