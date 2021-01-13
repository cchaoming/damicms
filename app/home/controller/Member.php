<?php

/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 前台列表页 Action
 *
 * @Filename MemberAction.class.php $
 *
 * @Author 追 影 QQ:279197963 $
 *
 * @Date 2011-11-18 08:42:11 $
 *************************************************************/

namespace app\home\controller;

use think\captcha\facade\Captcha;
use think\exception\ValidateException;
use think\facade\Session;
use until\Cart;
use until\Qqlogin;
use until\Wxlogin;

class Member extends Base
{
    private $qqconfig = [];
    private $wxconfig = [];

    function initialize()
    {
        parent::initialize();
        $this->qqconfig['appid'] = config('app.QQ_APPID');
        $this->qqconfig['appkey'] = config('app.QQ_APPKEY');
        $this->qqconfig['callback'] = 'http://' . $_SERVER['HTTP_HOST'] . "/index.php/Member/qqcallback";
        $this->wxconfig['appid'] = config('app.WX_APPID');
        $this->wxconfig['appsecret'] = config('app.WX_APPKEY');
        $this->wxconfig['callback'] = 'http://' . $_SERVER['HTTP_HOST'] . "/index.php/Member/wxcallback";
        $member_menu = S('member_menu');
        if (!is_array($member_menu)) {
            $member_menu = M('member_menu')->whereRaw('is_show=1')->orderRaw('drand asc')->select();
            S('member_menu', $member_menu);
        }
        $this->assign('member_menu', $member_menu);
    }

//检查是否登录
    private function is_login()
    {
        header("Content-type: text/html; charset=utf-8");
        if (!session('dami_uid')) {
            $lasturl = urlencode(htmlspecialchars('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
            $this->assign('jumpUrl', '/index.php/Member/login?lasturl=' . $lasturl);
            $this->success('未登陆或登陆超时，请重新登陆,页面跳转中~');
        }
    }

//用户登陆
    public function login()
    {

        $uid = session('dami_uid');
        if ($uid) {
            return $this->redirect('Member/main');
        }
        $refer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
        $lasturl = ($refer && (stripos($refer, 'dologin') !== false || stripos($refer, 'dologout') !== false || stripos($refer, 'doreg') !== false || stripos($refer, 'register') !== false)) ? '' : urlencode($refer);
        if (isset($_REQUEST['lasturl']) && strlen($_REQUEST['lasturl']) > 4) {
            $lasturl = $_REQUEST['lasturl'];
        }
        $this->assign('lasturl', $lasturl);
        return $this->display();
    }

//登录
    function dologin()
    {
        if (!Session::has('err_number')) {
            session('err_number', 0);
        }
        if (session('err_number') >= 3) {
            self::check_verify();
        }
        $username = inject_check($_REQUEST['username']);
        $userpwd = inject_check($_REQUEST['userpwd']);
        if ($username == '' || $userpwd == '') {
            $this->error('请输入用户名和密码?');
            exit();
        }
        $info = M('member')->whereRaw("username='{$username}' and is_lock=0")->find();
        if (!$info) {
            $this->error('用户不存在或账户未激活!');
        } else {
            if ($info['userpwd'] != md5(md5($userpwd))) {
                session('err_number', session('err_number') + 1);
                $this->error('密码错误，请重新登录!');
            } else {
                $this->login_session($info);
                if (!empty($_REQUEST['lasturl'])) {
                    $refer = $_REQUEST['lasturl'];
                    $lasturl = ($refer && (stripos($refer, 'login') !== false || stripos($refer, 'dologin') !== false || stripos($refer, 'dologout') !== false || stripos($refer, 'doreg') !== false || stripos($refer, 'register') !== false)) ? '' : urldecode($refer);
                    $lasturl = htmlspecialchars($lasturl);
                    $this->assign('jumpUrl', $lasturl);
                } else {
                    $this->assign('jumpUrl', U('Member/main'));
                }
                $this->success('登录成功~');
            }
        }
    }

    private function login_session($info)
    {
        session('err_number', 0);
        session('dami_uid', $info['id']);
        session('dami_username', $info['username']);
        session('dami_usericon', $info['icon']);
        session('dami_usergroup', $info['group_id']);
        session('dami_uservail', get_field('member_group', 'group_id=' . $info['group_id'], 'group_vail'));
    }

//在线充值
    function chongzhi()
    {
        self::is_login();
        $info = M('member')->whereRaw('id=' . intval(session('dami_uid')))->find();
        $this->assign('row', $info);
        return $this->display();
    }

//卡充值
    function card_money()
    {
        self::is_login();
        $uid = intval(session('dami_uid'));
        $User = M("card"); // 实例化User对象
        if ($this->request->isPost()) {
            $data = array_map('strval', $_POST);
            $data = loopxss($data);
            $card_number = $data['card_number'];
            $card_pwd = $data['card_pwd'];
            $t = $User->whereRaw("card_num='$card_number' and status=0")->find();
            if (!$t) {
                $this->error('卡号错误或已使用');
            }
            if ($t['card_pwd'] != $card_pwd) {
                $this->error('卡号密码有误!充值失败!');
            } else {
                M('member')->whereRaw('id=' . $uid)->inc('money', floatval($t['money']))->update();
                //logResult(M('dami_common_member',null)->getLastSql().'<BR>');
                $data = [];
                $data['uid'] = $uid;
                $data['addtime'] = time();
                $data['price'] = floatval($t['money']);
                $data['trade_no'] = $card_number;
                $data['remark'] = "用户用卡号:{$card_number}充值";
                $data['log_type'] = 0;
                M('moneyLog',true)->save($data);
                $this->success('恭喜您充值成功!');
            }
        }
    }

//注销登录
    function dologout()
    {
        session('dami_uid',null);
        session('dami_username',null);
        session('dami_usericon',null);
        session('dami_uservail',null);
        session(null);
        $this->success('注销成功~',U('Member/login'));
    }

//用户注册
    public function register()
    {
        return $this->display();
    }

//确认注册
    function doreg()
    {
        if ($this->request->isPost()) {
            if (intval(config('app.MOBILE_VERIFY')) == 1) {
                self::check_verify(1);
            }
            try {
                validate(\app\base\validate\Member::class)->check($this->request->post());
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                $this->error($e->getError());
            }
            $User = M("Member",true); // 实例化User对象
            $data = array_map('strval', $_POST);
            $data['userpwd'] = md5(md5($_POST['userpwd']));
            $data['money'] = 0;
            $config = config('basic');
            if (intval(config('app.MAIL_REG')) == 1) {
                $data['is_lock'] = 1;
                $body = '点击或复制以下链接,激活您的账号:<br><a href="http://' . $_SERVER['HTTP_HOST'] . '/' . U('Member/doactive', array('username' => $data['username'])) . '">http://' . $_SERVER['HTTP_HOST'] . '/' . U('Member/doactive', array('username' => $data['username'])) . '</a>';
                send_mail($data['email'], $config[sitetitle] . '用户', '用户注册激活邮件', $body);
                $message = "恭喜你注册成功，但需要邮件激活，请登陆您的邮箱激活!";
            } else {
                $message = "注册成功,请登录~";
                $data['is_lock'] = 0;
            }

            $data['group_id'] = intval($config['defaultmp']);
            $data['addtime'] = time();
            $User->save($data);
            $this->assign('jumpUrl', U('Member/login'));
            $this->success($message);
        }

    }

//找回密码
    function find_password()
    {
        if ($this->request->isPost()) {
            self::check_verify();
            $_POST = array_map('strval', $_POST);
            if (empty($_POST['username']) || empty($_POST['email']) || !preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $_POST['email'])) {
                $this->error('请输入用户名与注册邮件');
            }
            $map[] = ['username', '=', inject_check($_POST['username'])];
            $map['email'] = ['email', '=', inject_check($_POST['email'])];
            $t = M('member')->where($map)->find();
            if (!$t) {
                $this->error('用户名与邮件不匹配');
            } else {
                $map['hash'] = dami_encrypt(time());
                $map['addtime'] = time();
                M('findPassword',true)->save($map);
                $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . U('Member/reset_password', $map);
                $body = "您在" . date('Y-m-d H:i:s') . "提交了找回密码请求。请点击下面的链接重置密码（48小时内有效）。<br><a href=\"{$url}\" target=\"_blank\">{$url}</a>";
                send_mail($t['email'], $t['email'] . '用户', '用户找回密码邮件', $body);
                $this->assign("waitSecond", 30);
                $this->assign("jumpUrl", U('Member/login'));
                $this->success('找回密码成功！请在48小时内登陆邮箱重置密码!');
            }
        } else {
            return $this->display();
        }
    }

//重置密码
    function reset_password()
    {
        if ($_REQUEST['email'] == '' || $_REQUEST['username'] == '' || $_REQUEST['hash'] == '' || $_REQUEST['addtime'] == '') {
            $this->errpr('URL参数不完整');
        }
        $_REQUEST = array_map('strval', $_REQUEST);
        $map['username'] = inject_check($_REQUEST['username']);
        $map['email'] = inject_check($_REQUEST['email']);
        $map['hash'] = inject_check($_REQUEST['hash']);
        $map['addtime'] = inject_check($_REQUEST['addtime']);
        $t = M('find_password')->where($map)->find();
        if (!$t) {
            $this->error('URL参数不正确');
        } else {
            if (time > $t['addtime'] + 48 * 3600) {
                $this->error('URL已经过期');
                M('find_password')->removeOption()->whereRaw('id=' . $t['id'])->delete();
            }
        }
        if ($_POST) {
            if ($_POST['newpwd'] == '' || $_POST['newpwd'] != $_POST['newpwd2']) {
                $this->error('密码不能为空，两次密码输入必须一致');
            }
            unset($map['hash']);
            unset($map['addtime']);
            M('member',true)->where($map)->save(['userpwd' => md5(md5($_POST['newpwd']))]);
            $this->assign("jumpUrl", U('Member/login'));
            $this->success('密码已经修改成功！请登陆');
        } else {
            return $this->display();
        }
    }

//用户激活
    function doactive()
    {
        $username = inject_check($_REQUEST['username']);
        $t = M('member')->whereRaw("username='{$username}' and last_uptime is null")->find();
        if (!$t) {
            $this->error('邮件已过期或已经激活!');
        } else {
            $data['is_lock'] = 0;
            $data['last_uptime'] = time();
            M('member',true)->where("username='{$username}' and last_uptime is null")->save($data);
            $this->assign('jumpUrl', 'http://' . $_SERVER['HTTP_HOST']);
            $this->success('邮件激活，请登陆`');
        }
    }

    //生成验证码
    public function verify()
    {
        return Captcha::create();
    }

    //手机验证码
    public function sms_verify()
    {
        $mobile = $this->request->get('mobile');
        $this->ajaxReturn(send_smsmess($mobile, null, 1));
    }

//验证验证码(包括手机验证码)
    private function check_verify($type = 0)
    {
        if ($this->request->param('verify') && TMPL_NAME != config('app.DEFAULT_WAP_THEME')) {
            $this->error('验证码必须!');
        }
        if ($type == 0 && cookie('think_template') != config('app.DEFAULT_WAP_THEME')) {
            if (!captcha_check($this->request->post('verify'))) {
                $this->error('验证码错误!');
            }

        } else if (md5($this->request->post('verify')) != session('mobile_verify') && cookie('think_template') != config('app.DEFAULT_WAP_THEME')) {
            $this->error('验证码错误!');
        }
    }

//个人资料修改
    function main()
    {
        self::is_login();
        if ($this->request->isPost()) {
            $data = array_map('strval', $this->request->post());
            $data = loopxss($data);
            $data = array_map('htmlentities', $data);
            unset($data['username']);//禁止修改用户名
            unset($data['money']);//禁止修改money
            unset($data['is_lock']);//禁止修改锁定状态
            unset($data['group_id']);//禁止修改锁定状态
            $uid = session('dami_uid');
            $user_model = D("Member");
            $User = $user_model->find($uid); // 实例化User对象
            try {
                $User->save($data);
                $this->success('资料保存成功~');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                $this->error($e->getError());
            }
        } else {
            $info = M('member')->where('id=' . (int)session('dami_uid'))->find();
            $this->assign('info', $info);
            return $this->display();
        }
    }

//修改密码
    function changepwd()
    {
        self::is_login();
        if ($this->request->isPost()) {
            if (!$this->request->post('oldpwd')) {
                $this->error('请输入旧密码!');
            }
            if (!$this->request->post('newpwd') || $this->request->post('newpwd') != $this->request->post('newpwd2')) {
                $this->error('密码输入不一致!');
            }
            $info = M('member')->whereRaw("id=" . (int)session('dami_uid') . " and userpwd='" . md5(md5($_POST['oldpwd'])) . "'")->find();
            if (!$info) {
                $this->error('旧密码不正确!');
            } else {
                $data['id'] = session('dami_uid');
                $data['userpwd'] = md5(md5($this->request->post('newpwd')));
                M('member',true)->save($data);
                Session::clear();
                $this->assign('jumpUrl', U('Member/login'));
                $this->success('密码修改成功~,请重新登录!');
            }

        } else {
            return $this->display();
        }
    }

//投稿列表
    function tougaolist()
    {
        self::is_login();
        $list = M('article')->where('dami_uid=' . (int)session('dami_uid'))->select()->toArray();
        $this->assign('list', $list);
        $this->display();
    }

    function modpage()
    {
        self::is_login();
        $aid = intval($this->request->param('aid', 0));
        if ($this->request->isPost()) {
            //模拟关闭magic_quotes_gpc 不关闭有时视频用不起
            if (get_magic_quotes_gpc()) {
                $_POST = stripslashesRecursive($_POST);
            }
            $_POST['status'] = 0;
            $arc = M('article');
            $check = $this->request->checkToken(config('app.TOKEN_NAME'));
            if (false === $check) {
                $this->error('Token验证失败');
            }//防止乱提交表单
            $data = array_map('strval', $this->request->post());
            $data = loopxss($data);
            M('article',true)->whereRaw('dami_uid=' . (int)session('dami_uid') . ' and aid=' . $aid)->save($data);
            $this->assign('jumpUrl', U('Member/tougaolist'));
            $this->success('修改成功~,请等待审核!');
        } else {
            $info = M('article')->whereRaw('dami_uid=' . (int)session('dami_uid') . ' and aid=' . $aid)->find();
            if (!$info) {
                $this->error('记录不存在');
                exit();
            }
            self::pub_class($info['typeid']);
            $this->assign('info', $info);
        }
        return $this->display();
    }

    function delpage()
    {
        self::is_login();
        $aid = intval($this->request->param('aid'));
        M('article')->whereRaw('dami_uid=' . (int)session('dami_uid') . ' and status=0 and aid=' . $aid)->delete();
        $this->success('删除成功!');
    }

//用户投稿可以搞成游客投稿会员投稿只做简单演示表单按自己需求改进
    function tougao()
    {
        self::is_login();
        if ($_POST) {
            //模拟关闭magic_quotes_gpc 不关闭有时视频用不起
            if (get_magic_quotes_gpc()) {
                $_POST = stripslashesRecursive($_POST);
            }
            if ((!isset($_POST['verify']) || !$_POST['verify']) && !check_wap()) {
                $this->error('验证码必须!');
            }
            if (!captcha_check($this->request->post('verify')) && !check_wap()) {
                $this->error('验证码错误!');
            }
            $data = array_map('strval', $_POST);
            $data = loopxss($data);
//过滤下标题
            $data['title'] = htmlspecialchars($_POST['title']);
            $data['content'] = htmlspecialchars($_POST['content']);
            $data['status'] = 0;
            $data['addtime'] = date('Y-m-d H:i:s', time());
            $data['dami_uid'] = session('dami_uid');
            $arc = M('article');
            $this->verify_token();
            M('article',true)->save($data);
            $this->success('发布成功请等待管理员审核~');
        } else {
            self::pub_class();
            return $this->display();
        }
    }


//订单列表
    function buylist()
    {
        self::is_login();
        $dao = D('TradeView');
        $list = $dao->whereRaw('member_trade.uid=' . (int)session('dami_uid'))->select()->toArray();
        $this->assign('list', $list);
        $this->display();
    }

//提现
    function tixian()
    {
        self::is_login();
        if ($this->request->isPost()) {
            $money = floatval($this->request->post('money'));
            if (!$this->request->post('your_email') || $money <= 0) {
                $this->error('提现参数有错误!');
            }
            $have_money = M('member')->whereRaw('id=' . (int)session('dami_uid'))->value('money');
            if (floatval($have_money) < $money) {
                $this->error('提现金额大于您的余额,提现失败!');
            }
            $data = array_map('strval', $this->request->post());
            $data = loopxss($data);
            $data['status'] = 0;
            $data['uid'] = session('dami_uid');
            $data['addtime'] = time();
            $tx = M('tixian',true);
            $this->verify_token();
            $tx->save($data);
            unset($data);
            $this->success('提现申请成功，等待2-3个工作日处理!');
        }
    }

    //公共分类
    private function pub_class($type_value = 0)
    {
        $type = M('type');
        $count = [];
        $oplist = $type->whereRaw('islink=0 and isuser=1')->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select()->toArray();
        foreach ($oplist as $k => $v) {
            $check = '';
            if ($v['typeid'] == $type_value) {
                $check = 'selected="selected"';
            }
            if ($v['fid'] == 0) {
                $count[$k] = '';
            } else {
                for ($i = 0; $i < count(explode('-', $v['bpath'])) * 2; $i++) {
                    $count[$k] .= '&nbsp;';
                }
            }
            $op .= "<option value=\"" . $v['typeid'] . "\" $check>{$count[$k]}|-{$v['typename']}</option>";
        }
        $this->assign('op', $op);
    }

//购物第一步:确认订单
    function gobuy()
    {
        self::is_login();
        $lastbuy = M('member_trade')->whereRaw('uid=' . (int)session('dami_uid'))->orderRaw('addtime desc')->find();
        if ($lastbuy) {
            $info['realname'] = $lastbuy['sh_name'];
            $info['tel'] = $lastbuy['sh_tel'];
            $info['province'] = $lastbuy['province'];
            $info['city'] = $lastbuy['city'];
            $info['area'] = $lastbuy['area'];
            $info['address'] = $lastbuy['address'];
        } else {
            $info = M('member')->whereRaw('id=' . (int)session('dami_uid'))->find();
        }
        $this->assign('uinfo', $info);
        $iscart = $this->request->param('iscart');
        if ($iscart == 1) {
            $cart = new Cart();
            $list = $cart->contents();
            foreach ($list as $k => $v) {
                if ($v['id']) {
                    $list[$k]['gtype'] = $v['option']['gtype'];
                    $list[$k]['pic'] = $v['option']['pic'];
                    $list[$k]['id'] = $v['option']['gid'];
                }
            }
        } else {
            $_REQUEST['price'] = floatval(get_field('article', 'aid=' . intval($_REQUEST['id']), 'price'));
            $list = array(0 => $_REQUEST);
        }
        if (!$list) {
            $this->error('您的购物为空，请先选择物品!');
            exit();
        }
        $this->assign('list', $list);
        $this->display();
    }

//下单后付款
    function payagain()
    {
        self::is_login();
        $group_trade_no = $this->request->get('group_trade_no');
        $list = M('member_trade')->whereRaw("group_trade_no='{$group_trade_no}'")->select()->toArray();
        if ($list) {
            $trade_type = intval($list[0]['trade_type']);
            $total_fee = 0;
            $subject = '';
            foreach ($list as $k => $v) {
                $total_fee += $v['price'] * $v['num'];
                if (strlen($subject) < 200) {
                    $subject .= get_field('article', 'aid=' . $v['gid'], 'title');
                }
            }
            if ($trade_type == 0 || $trade_type == 2) {
                $this->error('该订单为货到付款无须支付!');
            }
            $new_trade_no = $group_trade_no . '-' . time();
            $post_data = array('trade_type' => $trade_type, "WIDtotal_fee" => $total_fee, "WIDsubject" => $subject, "WIDreceive_name" => $list[0]['sh_name'], "WIDreceive_address" => $list[0]['province'] . $list[0]['city'] . $list[0]['area'] . $list[0]['address'], "WIDreceive_mobile" => $list[0]['sh_tel'], "WIDreceive_phone" => "", "WIDout_trade_no" => $new_trade_no, "WIDshow_url" => "http://www.damicms.com/Public/donate", "WIDbody" => strip_tags('支付订单' . $group_trade_no), "WIDreceive_zip" => "", "WIDseller_email" => config("app.AP_EMAIL"));
            $code = config("app.TRADE_TYPE.{$trade_type}.title");
            $payment = new \payment\Payment($code);
            $payment->gateway($post_data);
        } else {
            $this->error('找不到订单');
        }
    }

//订单处理
    function dobuy()
    {
        self::is_login();
        if (!$this->request->isPost()) {
            exit();
        }
        if (!is_array($_POST['id'])) {
            $this->error('您的购物为空!');
            exit();
        }
        if ($_POST['realname'] == '' || $_POST['tel'] == '') {
            $this->error('收货人信息为空!');
            exit();
        }
        $trade_type = (int)$_POST['trade_type'];
        if (!config("app.TRADE_TYPE.{$trade_type}.code")) {
            $this->error('未知的支付方式!');
            exit();
        }
        $code = config("app.TRADE_TYPE.{$trade_type}.code");
        $iscart = (int)$_POST['iscart'];
        $group_trade_no = "GB" . time() . "-" . (int)session('dami_uid');
        if ($iscart == 1) {
            $cart = new Cart();
            $cart->destroy();
        }
        $_POST = loopxss($_POST);
        $trade = M('memberTrade',true);
        $this->verify_token();
//循环出购物车 写进数据库
        try {
            $title = '';
            $subject = '';
            $total_fee = 0;
            $total_num = 0;
            for ($i = 0; $i < count($_POST['id']); $i++) {
                if (!is_numeric($_POST['id'][$i]) || !is_numeric($_POST['price'][$i]) || !is_numeric($_POST['qty'][$i])) {
                    continue;
                }
                $data['gid'] = $_POST['id'][$i];
                $data['uid'] = session('dami_uid');
                $data['price'] = floatval(get_field('article', 'aid=' . intval($_POST['id'][$i]), 'price'));//$_POST['price'][$i];//信任客户端表单可以改写哈$_POST['price'][$i]
                $data['province'] = $_POST['province'];
                $data['city'] = $_POST['city'];
                $data['area'] = $_POST['area'];
                $data['sh_name'] = $_POST['realname'];
                $data['sh_tel'] = $_POST['tel'];
                $data['address'] = $_POST['address'];
                $data['group_trade_no'] = $group_trade_no;
                $data['out_trade_no'] = "DB" . time() . "-" . session('dami_uid');
                $data['servial'] = $_POST['gtype'][$i];
                $data['status'] = 0;
                $data['trade_type'] = $trade_type;
                $data['addtime'] = time();
                $data['num'] = abs(intval($_POST['qty'][$i]));
                $total_fee += ($data['num'] * $data['price']) * 1;
                $total_num += $data['num'];
                $trade->save($data);
                if (strlen($subject) < 200) {
                    $subject .= $_POST['name'][$i];
                }
                if (strlen($title) < 400) {
                    $title .= $_POST['name'][$i] . "&nbsp;&nbsp;数量:" . $data['num'] . ' 单价:' . $data['price'] . '<br>';
                }

            }
            if (intval(config('app.MAIL_TRADE')) == 1) {
                $config = config('basic');
                $user_name = $config[sitetitle] . '管理员';
                $subject = $config[sitetitle] . '订单提醒';
                $bodyurl = '下单时间：' . date('Y-m-d H:i:s', time()) . '<br>会员编号:' . session('dami_uid') . '<br>姓名：' . $_POST['realname'] . '<br>订单号：' . $group_trade_no . '<br>付款方式:' . config("app.TRADE_TYPE.{$trade_type}.title") . '<br>订购物件：<br>' . $title . '<br>总数量:' . $total_num . '<br>总金额:' . $total_fee . '元';
                $sendto_email = config('app.MAIL_TOADMIN');
                $email_port = config('app.MAIL_PORT');
                send_mail($sendto_email, $user_name, $subject, $bodyurl, $email_port);
            }
            if ($trade_type == 2) { //货到付款
                $this->assign('group_trade_no', $group_trade_no);
                $this->display('buysuccess');
            } else if ($trade_type == 3) {
                $have_money = get_field('member', 'id=' . (int)session('dami_uid'), 'money');
                if ($have_money < $total_fee) {
                    $this->assign('jumpUrl', U('Member/chongzhi'));
                    $this->error('您的余额不足，请充值!');
                    exit();
                }
//扣款
                M('member')->whereRaw('id=' . (int)session('dami_uid'))->dec('money', $total_fee)->update();
                $this->assign('group_trade_no', $group_trade_no);
                $this->display('buysuccess');
            } else {
                $post_data = array('trade_type' => $trade_type, "WIDtotal_fee" => $total_fee, "WIDsubject" => $subject, "WIDreceive_name" => $_POST['realname'], "WIDreceive_address" => $_POST['address'], "WIDreceive_mobile" => $_POST['tel'], "WIDreceive_phone" => "", "WIDout_trade_no" => $group_trade_no, "WIDshow_url" => "http://www.damicms.com/Public/donate", "WIDbody" => strip_tags('支付订单' . $group_trade_no), "WIDreceive_zip" => "", "WIDseller_email" => config("app.AP_EMAIL"));
                $payment = new \payment\Payment($code);
                $payment->gateway($post_data);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

    }

//删除交易记录
    function deltrade()
    {
        $buyid = intval($_REQUEST['buyid']);
        M('member_trade')->whereRaw('buy_id=' . $buyid . ' and uid=' . (int)session('dami_uid'))->delete();
//echo M('member_trade')->getLastSql();
        $this->success('删除成功!');
    }

//QQ登陆
    function qqlogin()
    {
        //$lasturl = urlencode(htmlspecialchars($_SERVER['HTTP_REFERER']));
        //$this->qqconfig['callback'] .= ('&lasturl=' . $lasturl);
        $o_qq = new Qqlogin($this->qqconfig);
        $o_qq->login();
    }

    //微信扫码登陆
    function wxlogin()
    {
        //$lasturl = urlencode(htmlspecialchars($_SERVER['HTTP_REFERER']));
        //$this->wxconfig['callback'] .= ('&lasturl=' . $lasturl);
        $wx = new Wxlogin($this->wxconfig);
        $wx->login();
    }

    function qqcallback()
    {
        $lasturl = urlencode(htmlspecialchars('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $o_qq = new Qqlogin($this->qqconfig);
        $o_qq->callback();
        $qid = $o_qq->get_openid();
        if ($qid) {
            $info = M('member')->whereRaw("qid='{$qid}'")->find();
            if ($info) {
//已经绑定帐号
                $this->login_session($info);
                if (!empty($_REQUEST['lasturl'])) {
                    $this->assign('jumpUrl', urldecode(htmlspecialchars($_REQUEST['lasturl'])));
                } else {
                    $this->assign('jumpUrl', U('Member/main'));
                }
                $this->success('登录成功~');
            } else {
//首次绑定
                $userinfo = $o_qq->get_user_info();
//print_r($userinfo);
                $this->assign('userinfo', $userinfo);
                $this->assign('qid', $qid);
                $this->display();
            }
        }
    }

    public function call_wxbind($openid, $userinfo = null)
    {
        if ($openid != '') {
            $info = M('member')->whereRaw("wx_openid='{$openid}'")->find();
            if ($info) {
                //已经绑定帐号
                $this->login_session($info);
                if (!empty($_REQUEST['lasturl'])) {
                    $this->assign('jumpUrl', urldecode(htmlspecialchars($_REQUEST['lasturl'])));
                } else {
                    $this->assign('jumpUrl', U('Member/main'));
                }
                $this->success('登录成功~');
            } else { //首次绑定
                $this->assign('userinfo', $userinfo);
                $this->assign('wx_openid', $openid);
                return $this->display('qqcallback');
            }
        } else {
            $this->error('参数错误');
        }
    }

    //微信授权返回
    function wxcallback()
    {
        $wx = Wxlogin::getInstance($this->wxconfig);
        $userinfo = $wx->get_user_info();
        $openid = $userinfo->openid;
        self::call_wxbind($openid, $userinfo);
    }

    //微信里的微信绑定
    function bindwx()
    {
        $openid = $this->request->param('openid');
        if ($openid) {
            self::call_wxbind($openid);
        }
    }

//创建帐号
    function qqcreate()
    {
        if ($this->request->isPost()) {
            $data = array_map('strval', $this->request->post());
            $data = loopxss($data);
            unset($data['money']);//禁止修改money
            unset($data['is_lock']);//禁止修改锁定状态
            if ($data['realname'] == '' || ($data['wx_openid'] == '' && $data['qid'] == '')) {
                $this->error('参数错误!');
                exit();
            }
            $t = M('member')->whereRaw("username='" . strval($data['realname']) . "'")->find();
            if ($t) {
                $data['username'] = $data['realname'];
            } else {
                $data['username'] = (string)time();
            }
            $data['userpwd'] = md5(md5(time() . rand(0, 9999)));
            try {
                validate(\app\base\validate\Member::class)->check($data);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                $this->error($e->getError());
            }
            $config = config('basic');
            $data['group_id'] = intval($config['defaultmp']);
            $dao = M('member',true);
            $dao->save($data);
            $uid = $dao->getLastInsID();
            $data['id'] = $uid;
            $this->login_session($data);
            if (!empty($_REQUEST['lasturl'])) {
                $this->assign('jumpUrl', urldecode(htmlspecialchars($_REQUEST['lasturl'])));
            } else {
                $this->assign('jumpUrl', U('Member/main'));

                $this->success('绑定成功,正在登陆~');
            }
        }
    }

//绑定帐号
    function qqupdate()
    {
        if ($this->request->isPost()) {
            $username = inject_check($_POST['username']);
            $userpwd = $_POST['userpwd'];
            $qid = $_POST['qid'];
            $openid = $_POST['openid'];
            $icon = $_POST['icon'];
            if ($username == '' || $userpwd == '' || ($openid == '' && $qid == '')) {
                $this->error('请输入用户名和密码?');
                exit();
            }
            $info = M('member')->whereRaw("username='{$username}' and is_lock=0")->find();
            if (!$info) {
                $this->error('用户不存在或已经禁止登陆!');
            } else {
                if ($info['userpwd'] != md5(md5($userpwd))) {
                    $this->error('密码错误，绑定失败!');
                } else {
                    $this->login_session($info);
                    if ($qid != '') {
                        $data['qid'] = $qid;
                    }
                    if ($openid != '') {
                        $data['openid'] = $openid;
                    }
                    if ($icon != '') {
                        $data['icon'] = $icon;
                    }
                    M('member',true)->whereRaw("username='{$username}' and is_lock=0")->save($data);
                    if (!empty($_REQUEST['lasturl'])) {
                        $this->assign('jumpUrl', urldecode(htmlspecialchars($_REQUEST['lasturl'])));
                    } else {
                        $this->assign('jumpUrl', U('Member/main'));
                    }
                    $this->success('绑定成功,正在登陆~');
                }
            }
        }
    }

//收藏夹列表
    function fav()
    {
        $this->title = '我的收藏';
        $list = D('FavoritesView')->getTableInstance()->whereRaw('favorites.uid=' . (int)session('dami_uid'))->select()->toArray();
        $this->list = $list;
        $this->display();
    }

//加入收藏夹
    function fav_save()
    {
        if (!session('dami_uid')) {
            $this->ajaxReturn(array('status' => 0, 'info' => '您还没有登录，请登录!'));
        }
        $aid = intval($this->request->param('aid'));
        if ($aid > 0) {
            $t = M('favorites')->whereRaw('aid=' . $aid)->find();
            if ($t) {
                $this->ajaxReturn(array('status' => 0, 'info' => '您已经收藏过该文章!'));
            } else {
                $data['aid'] = $aid;
                $data['uid'] = (int)session('dami_uid');
                $data['addtime'] = time();
                M('favorites',true)->save($data);
                $this->ajaxReturn(array('status' => 1, 'info' => '收藏成功!'));
            }
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => '文章ID错误,收藏失败!'));
        }
    }

//删除收藏夹
    function fav_del()
    {
        if (!session('dami_uid')) {
            $this->ajaxReturn(array('status' => 0, 'info' => '您还没有登录，请登录!'));
        }
        $aid = intval($_REQUEST['aid']);
        M('favorites')->whereRaw('aid=' . $aid . ' and uid=' . (int)session('dami_uid'))->delete();
        $this->ajaxReturn(array('status' => 1, 'info' => '收藏删除成功!'));
    }
//类结束
}