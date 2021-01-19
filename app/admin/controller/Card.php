<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 会员卡管理
 *
 * @Filename LinkAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-23 10:26:03 $
 *************************************************************/

namespace app\admin\controller;

use until\Page;
use until\MiString;

class Card extends Common
{
    private $model;

    protected function initialize()
    {
        parent::initialize();
        $this->model = D('card');
    }

    public function index()
    {
        $where = '1=1';
        $count = $this->model->whereRaw($where)->count();
        $p = new Page($count, 20);//分页条数
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '条记录');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
        $list = $this->model->limit($p->firstRow, $p->listRows)->select()->toArray();
        $this->assign('list', $list);
        $this->assign('page', $p->show());
        return $this->display();
    }

    public function add()
    {
        return $this->display('add');
    }

    public function doadd()
    {
        if ($this->request->isPost()) {
            $card_long = intval($_POST['card_long']);
            $pwd_long = intval($_POST['pwd_long']);
            $num = intval($_POST['num']);
            $money = floatval($_POST['money']);
            $cur_num = 0;
            $dao = M('card');
            while ($cur_num < $num) {
                $card_number = MiString::rand_string($card_long, 1);
                $card_pwd = MiString::rand_string($pwd_long, 5);
                $t = $dao->whereRaw("card_number='$card_number'")->find();
                if (!$t) {
                    $data['card_number'] = $card_number;
                    $data['card_pwd'] = $card_pwd;
                    $data['status'] = 0;
                    $data['addtime'] = time();
                    $data['money'] = $money;
                    M('card')->save($data);
                    $cur_num++;
                }
            }
            $this->success('制卡成功!', U('Card/index'));
        }
    }

    public function del()
    {
        $id = $this->request->param('id');
        if ($this->model->whereRaw('id in(' . $id . ')')->delete()) {
            $this->success('操作成功!',U('Card/index'));
        }
        $this->error('操作失败!');
    }

    public function cuse()
    {
        $id = $this->request->param('id');
        $status = (int)$this->request->param('status');
        if ($this->model->whereRaw('id in(' . $id . ')')->find()->save(['status' => $status])) {
            $this->success('操作成功!',U('Card/index'));
        }
        $this->error('操作失败!');
    }
}

?>