<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 文章管理
 *
 * @Filename ArticleAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-27 08:52:44 $
 *************************************************************/

namespace app\admin\controller;
class Apk extends Common
{
    //配置
    function config()
    {
        $dom = new \DOMDocument();
        $dom->load('./config.xml');
        if (!$dom) {
            $this->error('XML配置路径错误！');
            exit();
        }
        $list = array();
        $list['company'] = $dom->getElementsByTagName('company')->item(0)->nodeValue;
        $list['icon'] = $dom->getElementsByTagName('icon')->item(0)->nodeValue;
        $list['start_pic'] = $dom->getElementsByTagName('start_pic')->item(0)->nodeValue;
        $list['descript'] = $dom->getElementsByTagName('descript')->item(0)->nodeValue;
        $this->assign('list', $list);
        return $this->display();
    }

    //保存配置
    function doconfig()
    {
        if($this->request->isPost()){
        $company = $_POST['company'];
        $icon = $_POST['icon_img'];
        $start_pic = $_POST['start_img'];
        $descript = $_POST['content'];
        $dom = new \DOMDocument();
        $dom->load('./config.xml');
        if (!$dom) {
            $this->error('XML配置路径错误！');
            exit();
        }
        $dom->getElementsByTagName('company')->item(0)->nodeValue = $company;
        $dom->getElementsByTagName('icon')->item(0)->nodeValue =  $icon;
        $dom->getElementsByTagName('start_pic')->item(0)->nodeValue =  $start_pic;
        $dom->getElementsByTagName('descript')->item(0)->nodeValue = $descript;
        $dom->save('./config.xml');
        $this->success('保存成功!');
        }
    }

    //vip账号配置
    function vip_config()
    {
        $list = M('vip_mess')->find();
        $this->assign('list', $list);
        return $this->display();
    }

    //保存账号配置
    function dovip_config()
    {
//验证
        if($this->request->isPost()) {
            $url = config('app.SERVER_URL') . "/Public/check_webvip?vip_name=" . $_POST['vip_name'] . "&vip_pwd=" . $_POST['vip_pwd'];
            $res = json_decode(get_url_contents($url));
            if ($res->status == 0) {
                $this->error('对不起，您不是VIP客户!更多请参看www.damicms.com');
            }
            $dao = M('vipMess',true);
            $data = $_POST;
            $list = $dao->whereRaw('1=1')->orderRaw('id asc')->find();
            if ($list) {
                $list->save($data);
            } else {
                $dao->save($data);
            }
        }
        $this->success('保存成功!');
    }

//模板下载
    function moban()
    {
        $dao = M('vip_mess')->find();
        if($dao){
        header('Location:' .config('app.SERVER_URL') . 'Moban?vip_name=' . $dao['vip_name'] . '&vip_pwd=' . $dao['vip_pwd']);
        }else{
            $this->error('请升级VIP','https://www.damicms.com/Public/donate');
        }
    }

//下载和生成
    function down()
    {
        $port = $_SERVER['SERVER_PORT'];
        if ($port == 80) {
            $url = "http://" . $_SERVER['SERVER_NAME'] . '/';
        } else {
            $url = "http://" . $_SERVER['SERVER_NAME'] . ':' . $port . '/';
        }
        $go_url = config('app.SERVER_URL') . "Build/index?url=" . urlencode($url);
        header("Location:{$go_url}");
    }

//文章模块 添加-栏目option
    private function addop()
    {
        $type = M('type');
        $option = '';
        //获取栏目option
        $list = $type->whereRaw('islink=0')->field("typeid,typename,fid,concat(path,'-',typeid) as bpath")->group('bpath')->select()->toArray();
        foreach ($list as $k => $v) {
            $check = '';
            if (isset($_REQUEST['typeid'])) {
                if ($v['typeid'] == intval($_REQUEST['typeid'])) {
                    $check = 'selected="selected"';
                }
            }
            $count[$k] = '';
            if ($v['fid'] != 0) {
                for ($i = 0; $i < count(explode('-', $v['bpath'])) * 2; $i++) {
                    $count[$k] .= '&nbsp;';
                }
            }
            $option .= "<option value=\"{$v['typeid']}\" $check>{$count[$k]}|-{$v['typename']}</option>";
        }
        $this->assign('option', $option);
    }

    function push_bcs()
    {
        return $this->display();
    }
//结束 
}

?>