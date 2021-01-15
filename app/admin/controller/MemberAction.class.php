<?php

/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 广告管理
 *
 * @Filename AdAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-23 09:59:26 $
 *************************************************************/
class MemberAction extends CommonAction
{
//支付宝配置
    function doap()
    {
        if (empty($_POST['ap_email']) || empty($_POST['ap_pid']) || empty($_POST['ap_key'])) {
            $this->error('配置参数不能为空');
        }
        //改写配置文件
        $config_file = "./Public/Config/config.ini.php";
        $fp = fopen($config_file, "r");
        $configStr = fread($fp, filesize($config_file));
        fclose($fp);
        $configStr = preg_replace("/'AP_EMAIL'=>'.*'/", "'AP_EMAIL'=>'" . htmlspecialchars($_POST['ap_email'],ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'AP_PID'=>'.*'/", "'AP_PID'=>'" . htmlspecialchars($_POST['ap_pid'],ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'AP_KEY'=>'.*'/", "'AP_KEY'=>'" . htmlspecialchars($_POST['ap_key'],ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'AP_TYPE'=>'.*'/", "'AP_TYPE'=>'" . htmlspecialchars($_POST['ap_type'],ENT_QUOTES) . "'", $configStr);
        $fp = fopen($config_file, "w") or die("<script>alert('写入配置失败，请检查安装目录/Public/Config/config.ini.php是否可写入！');history.go(-1);</script>");
        fwrite($fp, $configStr);
        fclose($fp);
        //清理缓存
        $Webpath = './Web/Runtime/';
        $Adminpath = './Admin/Runtime/';
        if (is_dir($Webpath)) {
            @deldir($Webpath);
        }
        if (is_dir($Adminpath)) {
            @deldir($Adminpath);
        }
        //改写淘宝配置文件
        $ap_path = (intval($_POST['ap_type']) == 1 ? 'ap_jishi' : 'ap_danbao');
        $taobao_config = "./Trade/{$ap_path}/alipay.config.php";
        $fp = fopen($taobao_config, "r");
        $configStr = fread($fp, filesize($taobao_config));
        fclose($fp);
        $configStr = preg_replace("/alipay_config\['partner'\]='.*'/", "alipay_config['partner']='" . htmlspecialchars($_POST['ap_pid'],ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/alipay_config\['key'\]='.*'/", "alipay_config['key']='" . htmlspecialchars($_POST['ap_key'],ENT_QUOTES) . "'", $configStr);
        $fp = fopen($taobao_config, "w") or die("<script>alert('写入配置失败，请检查安装目录/Trade/{$ap_path}/alipay.config.php是否可写入！');history.go(-1);</script>");
        fwrite($fp, $configStr);
        fclose($fp);
        //改写处理url
        $taobao_config = "./Trade/{$ap_path}/alipayapi.php";
        $fp = fopen($taobao_config, "r");
        $configStr = fread($fp, filesize($taobao_config));
        fclose($fp);

        $configStr = preg_replace("/notify_url='.*'/", "notify_url='http://" . $_SERVER['HTTP_HOST'] . __ROOT__ . "/index.php/Public/shouquan'", $configStr);
        $configStr = preg_replace("/return_url='.*'/", "return_url='http://" . $_SERVER['HTTP_HOST'] . __ROOT__ . "/Trade/{$ap_path}/return_url.php'", $configStr);
        $fp = fopen($taobao_config, "w") or die("<script>alert('写入配置失败，请检查安装目录/Trade/alipayapi.php是否可写入！');history.go(-1);</script>");
        fwrite($fp, $configStr);
        fclose($fp);
        $this->success('支付宝在线配置成功!');
    }

//微信基础配置
    function do_wxset()
    {
        if (empty($_POST['wx_appid']) || empty($_POST['wx_appkey']) || empty($_POST['wx_token']) || empty($_POST['wx_jqrkey'])) {
            $this->error('配置参数不能为空');
        }
        $wx_trade = intval($_POST['wx_trade']);
        $wx_login = intval($_POST['wx_login']);
        //改写配置文件
        $config_file = "./Public/Config/config.ini.php";
        $config_pay = "./Trade/Wxpay/lib/WxPay.Config.php";
        $fp = fopen($config_file, "r");
        $configStr = fread($fp, filesize($config_file));
        fclose($fp);
        $configStr = preg_replace("/'WX_APPID'=>'.*'/", "'WX_APPID'=>'" . htmlspecialchars($_POST['wx_appid'],ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'WX_APPKEY'=>'.*'/", "'WX_APPKEY'=>'" . htmlspecialchars($_POST['wx_appkey'],ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'WX_TOKEN'=>'.*'/", "'WX_TOKEN'=>'" . htmlspecialchars($_POST['wx_token'],ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'WX_JQRKEY'=>'.*'/", "'WX_JQRKEY'=>'" . htmlspecialchars($_POST['wx_jqrkey'],ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'WX_TRADE'=>'.*'/", "'WX_TRADE'=>'" . $wx_trade . "'", $configStr);
        $configStr = preg_replace("/'WX_LOGIN'=>'.*'/", "'WX_LOGIN'=>'" . $wx_login . "'", $configStr);
        $fp = fopen($config_file, "w") or die("<script>alert('写入配置失败，请检查安装目录/Public/Config/config.ini.php是否可写入！');history.go(-1);</script>");
        fwrite($fp, $configStr);
        fclose($fp);
        if ($wx_trade == 1) {
            $fp = fopen($config_pay, "r");
            $configStr = fread($fp, filesize($config_pay));
            fclose($fp);
            $configStr = preg_replace("/APPID='.*'/", "APPID='" . htmlspecialchars($_POST['wx_appid'],ENT_QUOTES) . "'", $configStr);
            $configStr = preg_replace("/MCHID='.*'/", "MCHID='" . htmlspecialchars($_POST['wx_mchid'],ENT_QUOTES) . "'", $configStr);
            $configStr = preg_replace("/KEY='.*'/", "KEY='" . htmlspecialchars($_POST['wx_paykey'],ENT_QUOTES) . "'", $configStr);
            $configStr = preg_replace("/APPSECRET='.*'/", "APPSECRET='" . htmlspecialchars($_POST['wx_appkey'],ENT_QUOTES) . "'", $configStr);
            $configStr = preg_replace("/NOTIFY_URL='.*'/", "NOTIFY_URL='http://" . $_SERVER['HTTP_HOST'] . "/index.php/Public/wxnotify'", $configStr);
            $fp = fopen($config_pay, "w") or die("<script>alert('写入配置失败，请检查安装目录/Trade/Wxpay/lib/WxPay.Config.php是否可写入！');history.go(-1);</script>");
            fwrite($fp, $configStr);
            fclose($fp);
        }
        //
        //清理缓存
        $Webpath = './Web/Runtime/';
        $Adminpath = './Admin/Runtime/';
        if (is_dir($Webpath)) {
            @deldir($Webpath);
        }
        if (is_dir($Adminpath)) {
            @deldir($Adminpath);
        }
        $this->success('微信基础配置成功!');
    }

//微信自定义菜单
    function wx_menu()
    {
        $dao = M('wx_menu');
        if ($_POST) {
            for ($i = 1; $i <= 3; $i++) {
                //一级菜单
                //只有一级
                if (!empty($_POST['value_0_' . $i])) {
                    $data['id'] = $i;
                    $data['menu_name'] = $_POST['key_' . $i];
                    $data['menu_value'] = $_POST['value_0_' . $i];
                    $data['menu_type'] = intval($_POST['type_0_' . $i]);
                    $data['pid'] = 0;
                    $t = $dao->where('id=' . $i)->find();
                    if (!$t) {
                        $dao->add($data);
                    } else {
                        $dao->where('id=' . $i)->save($data);
                    }
                } else {
                    //增加顶级
                    $data['id'] = $i;
                    $data['menu_name'] = $_POST['key_' . $i];
                    $data['menu_value'] = '';
                    $data['menu_type'] = 2;
                    $data['pid'] = 0;
                    $t = $dao->where('id=' . $i)->find();
                    if (!$t) {
                        $dao->add($data);
                    } else {
                        $dao->where('id=' . $i)->save($data);
                    }
                    //循环post取出子菜单
                    foreach ($_POST as $k => $v) {
                        if (strpos($k, 'subkey_' . $i . '_') !== false) {
                            $arr = explode('_', $k);
                            $index = intval($arr[2]);
                            $data['id'] = $index;
                            $data['menu_name'] = $_POST['subkey_' . $i . '_' . $index];
                            $data['menu_value'] = $_POST['value_' . $i . '_' . $index];
                            $data['menu_type'] = intval($_POST['type_' . $i . '_' . $index]);
                            $data['pid'] = $i;
                            $t = $dao->where('id=' . $index)->find();
                            if (!$t) {
                                $dao->add($data);
                            } else {
                                $dao->where('id=' . $index)->save($data);
                            }
                        }
                    }
                }
            }
            $this->success('微信菜单设置成功！');
        } else {
            $list = $dao->select();
            foreach ($list as $k => $v) {
                $level = $v['pid'] == 0 ? 0 : 1;
                $enable = $v['pid'] == 0 ? $v['pid'] . '_' . $v['id'] : '0_' . $v['pid'] . '_' . $v['id'];
                $ret[] = array('index' => $v['id'], 'level' => $level, 'name' => $v['menu_name'], 'key' => $v['menu_value'], 'enable' => $enable, 'parentId' => $v['pid'], 'type' => $v['menu_type']);
            }
            $list_str = addslashes(json_encode($ret));
			//var_dump($list_str);
            $this->assign('list_str', $list_str);
            $this->display();
        }
    }

//ajax删除微信菜单
    function ajax_wxdel()
    {
        $id = intval($_REQUEST['id']);
        if ($id != 0) {
            M('wx_menu')->where('id=' . $id)->delete();
        }
    }

    function doqqset()
    {
        //改写配置文件
        $config_file = "./Public/Config/config.ini.php";
        $fp = fopen($config_file, "r");
        $configStr = fread($fp, filesize($config_file));
        fclose($fp);
        $configStr = preg_replace("/'QQ_APPID'=>'.*'/", "'QQ_APPID'=>'" . htmlspecialchars($_POST['qq_appid'],ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'QQ_APPKEY'=>'.*'/", "'QQ_APPKEY'=>'" . htmlspecialchars($_POST['qq_appkey'],ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'QQ_LOGIN'=>'.*'/", "'QQ_LOGIN'=>'" . htmlspecialchars($_POST['qq_login'],ENT_QUOTES) . "'", $configStr);
        $fp = fopen($config_file, "w") or die("<script>alert('写入配置失败，请检查安装目录/Public/Config/config.ini.php是否可写入！');history.go(-1);</script>");
        fwrite($fp, $configStr);
        fclose($fp);
        //清理缓存
        $Webpath = './Web/Runtime/';
        $Adminpath = './Admin/Runtime/';
        if (is_dir($Webpath)) {
            @deldir($Webpath);
        }
        if (is_dir($Adminpath)) {
            @deldir($Adminpath);
        }
        $this->success('QQ快捷登陆设置成功!');

    }


//订单列表
    function cartlist()
    {
        $model = D('TradeView');
        import('ORG.Util.Page');
        $where = '';
        if (!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])) {
            $where .= 'member_trade.addtime>=' . strtotime($_REQUEST['start_time'] . " 00:00:00") . ' and member_trade.addtime<=' . strtotime($_REQUEST['end_time'] . " 23:59:59") . " and ";
        } else if (!empty($_REQUEST['start_time']) && empty($_REQUEST['end_time'])) {
            $where .= 'member_trade.addtime>=' . strtotime($_REQUEST['start_time'] . " 00:00:00") . ' and member_trade.addtime<=' . strtotime($_REQUEST['start_time'] . " 23:59:59") . " and ";
        } else if (empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])) {
            $where .= 'member_trade.addtime>=' . strtotime($_REQUEST['end_time'] . " 00:00:00") . ' and member_trade.addtime<=' . strtotime($_REQUEST['end_time'] . " 23:59:59") . " and ";
        }
        if (!empty($_REQUEST['keyword'])) {
            $where .= "article.title like '%" . htmlspecialchars(trim($_REQUEST['keyword'])) . "%' and ";
        }
        $where .= '1=1';
        $count = $model->where($where)->count();
        $p = new Page($count, 20);
        $list = $model->field('sum(member_trade.price*member_trade.num) as sub_total_price,member_trade.*')->where($where)->group('group_trade_no')->order('member_trade.addtime desc')->limit($p->firstRow . ',' . $p->listRows)->select();

       //echo $model->getLastSql();

        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '篇文章');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>");
        $this->assign('page', $p->show());
        $this->assign('list', $list);
        $this->display();
    }

//订单列表
    function userlist()
    {
        $model = M('member');
        import('ORG.Util.Page');
        $where = '';
        if (!empty($_REQUEST['keyword'])) {
            $where .= "(username like '%" . htmlspecialchars(trim($_REQUEST['keyword'])) . "%' or realname like '%" . htmlspecialchars(trim($_REQUEST['keyword'])) . "%' or address like '%" . htmlspecialchars(trim($_REQUEST['keyword'])) . "%') and ";
        }
        $where .= '1=1';
        $count = $model->where($where)->count();
        $p = new Page($count, 20);
        $list = $model->where($where)->order('addtime desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '篇文章');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>");
        $this->assign('page', $p->show());
        $this->assign('list', $list);
        $this->display();
    }

//删除交易记录
    function deltrade()
    {
        $buyid = strval($_REQUEST['buyid']);
        M('member_trade')->where('group_trade_no=\'' . $buyid.'\'')->delete();
        $this->success('删除成功!');
    }

    function deluser()
    {
        $id = intval($_REQUEST['id']);
        M('member')->where('id=' . $id)->delete();
        $this->success('删除成功!');
    }

    function deltixian()
    {
        $id = intval($_REQUEST['id']);
        M('tixian')->where('id=' . $id)->delete();
        $this->success('删除成功!');
    }

//ajax改变电商状态
    function ajax_change_trade()
    {
        $buyid = strval($_REQUEST['buyid']);
        $status = intval($_REQUEST['status']);
        if ($buyid == '') {
            $this->ajaxReturn('参数错误', '提示', 0);
            exit();
        }
        $t = M('member_trade')->where('group_trade_no=\'' . $buyid.'\'')->setField('status', $status);
        //echo M('member_trade')->getLastSql();
        $this->ajaxReturn('操作成功','提示',1);
    }

    function moduser()
    {
        $id = intval($_REQUEST['id']);
        if ($_POST) {
            $data = $_POST;
            if ($_POST['userpwd1'] == $_POST['userpwd2'] && trim($_POST['userpwd1']) != '' && trim($_POST['userpwd2']) != '') {
                $data['userpwd'] = md5(md5($_POST['userpwd1']));
            }
            M('member')->save($data);
            $this->success('资料保存成功~');
        } else {
            $info = M('member')->where('id=' . $id)->find();
            $this->assign('info', $info);
            $this->display();
        }
    }
	
	function adduser()
    {
        if ($_POST) {
            $data = $_POST;
            if ($_POST['userpwd'] == $_POST['userpwd2'] && trim($_POST['userpwd']) != '' && trim($_POST['userpwd2']) != '') {
                $data['userpwd'] = md5(md5($_POST['userpwd']));
            }
            $User = D("Member"); // 实例化User对象
            if (!$User->create()) {
                $this->error($User->getError());
            } else {
                $User->add($data);
                $this->success('添加用户成功~', U('Member/userlist'));
            }
        } else {
            $this->display();
        }
    }

//群发邮件
    function send_mail()
    {
        if ($_POST) {
            if ($_POST['title'] == '' || $_POST['content'] == '') {
                $this->error('请输入标题和内容!');
                exit();
            }
            $where = '';
            if ($_POST['except_user'] != '') {
                $_POST['except_user'] = str_replace("，", ",", $_POST['except_user']);
                $e_arr = explode(",", $_POST['except_user']);
                foreach ($e_arr as $v) {
                    $n_arr[] = ("'" . $v . "'");
                }
                $username = join(',', $n_arr);
                $where = "username not in({$username})";
            }
            $list = M('member')->where($where)->select();
            foreach ($list as $k => $v) {
                $r = send_mail($v['email'], $v['username'], $_POST['title'], $_POST['content'], C('MAIL_PORT'));
                if ($r) {
                    echo '正在发送' . $v['email'] . ',发送成功(√)<br>';
                } else {
                    echo '正在发送' . $v['email'] . ',发送失败(ㄨ)<br>';
                }
                ob_flush();
                flush();
            }
			ob_end_flush();//清空浏览器缓存
        } else {
            $this->display();
        }
    }

    function showuser()
    {
        $id = intval($_REQUEST['id']);
        $info = M('member')->where('id=' . $id)->find();
        $this->assign('info', $info);
        $this->display();
    }

//添加、修改用户组
    function addgroup()
    {
        if ($_POST) {
            $data = $_POST;
            $data['group_vail'] = implode(',', $_POST['typeids']);
            if (isset($data['group_id'])) {
                M('member_group')->save($data);
                $this->assign("jumpUrl", U('Member/usergroup'));
                $this->success('修改组成功');
            } else {
                $gid = M('member_group')->add($data);
                if ($gid <= 0) {
                    $this->error('添加会员组失败!');
                } else {
                    $this->assign("jumpUrl", U('Member/usergroup'));
                    $this->success('添加会员组成功!');
                }
            }
        } else {
            $group_id = intval($_REQUEST['group_id']);
            if ($group_id != 0) {
                $info = M('member_group')->where('group_id=' . $group_id)->find();
                $this->assign('info', $info);
            }
            $type_tree = M('type')->where('islink=0')->select();
            $this->assign('type_tree', $type_tree);
            $this->display();
        }
    }

//删除用户组
    function delgroup()
    {
        $group_id = (int)$_GET['group_id'];
        if ($group_id != 0) {
            M('member_group')->where('group_id=' . $group_id)->delete();
            M('member')->where('group_id=' . $group_id)->setField('group_id', 0);
        }
        $this->success('删除用户组成功!');

    }

//提现列表
    function tixianlist()
    {
        $model = D('TixianView');
        import('ORG.Util.Page');
        $where = '';
        if (!empty($_REQUEST['keyword'])) {
            $where .= "(username like '%" . htmlspecialchars(trim($_REQUEST['keyword'])) . "%' or realname like '%" . htmlspecialchars(trim($_REQUEST['keyword'])) . "%') and ";
        }
        $where .= '1=1';
        $count = $model->where($where)->count();
        $p = new Page($count, 20);
        $list = $model->where($where)->order('addtime desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '篇文章');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>");
        $this->assign('page', $p->show());
        $this->assign('list', $list);
        $this->display();
    }

//提现处理
    function dotixian()
    {
        $id = intval($_REQUEST['id']);
        $info = M('tixian')->where('id=' . $id)->find();
        if (!$info) {
            $this->error('信息不存在!');
            exit();
        }
        M('tixian')->where('id=' . $id)->setField('status', 1);
        M('member')->where('id=' . $info['uid'])->setDec('money', $info['money']);
        $this->success('提现处理成功!!');
    }

//导出会员到excel
    function goexcel()
    {
        import('ORG.Util.PHPExcel');//引入PHPExcel类
        $fileName = 'member' . date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $objPHPExcel = new PHPExcel();
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
        $title_array = array(
            'id' => '用户编号',
            'username' => '用户名',
            'realname' => '姓名',
            'sex' => '性别',
            'tel' => '联系电话',
            'qq' => 'QQ',
            'email' => '电子邮件',
            'birthday' => '出生日期',
            'province' => '所在省份',
            'city' => '所在城市',
            'address' => '详细地址',
            'addtime' => '注册时间'
        );
        $data_array = M('member')->where('is_lock=0')->select();
        $cellNum = count($title_array);
        $dataNum = count($data_array);
//输出表头
        $objPHPExcel->setActiveSheetIndex(0);
        $i = 0;
        foreach ($title_array as $k => $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '1', (string)$title_array[$k]);
            $i++;
        }
//输出内容
        for ($i = 0; $i < $dataNum; $i++) {
            $j = 0;
            foreach ($title_array as $k => $v) {
                $data_value = $data_array[$i][$k];
                if ($k == 'addtime' && $data_value != '') {
                    $data_value = date('Y-m-d H:i:s', $data_value);
                } //对注册时间特殊处理下
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$j] . ($i + 2), (string)$data_value);
                $j++;
            }
        }
// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('会员表');
        $objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client's web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
//类结束
}

?>