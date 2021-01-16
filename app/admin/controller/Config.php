<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 系统全局配置
 *
 * @Filename ConfigAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-25 20:26:12 $
 *************************************************************/

namespace app\admin\controller;
class Config extends Common
{
    public function index()
    {
        $type = M('config');
        $list = $type->where('id=1')->find();
        $this->assign('list', $list);
        return $this->display('index');
    }

    public function update()
    {
        $data['sitetitle'] = trim($_POST['sitetitle']);
        $data['sitetitle2'] = trim($_POST['sitetitle2']);
        $data['siteurl'] = trim($_POST['siteurl']);
        $data['updown'] = trim($_POST['updown']);
        $data['xgwz'] = trim($_POST['xgwz']);
        $data['sitedescription'] = trim($_POST['sitedescription']);
        $data['sitekeywords'] = trim($_POST['sitekeywords']);
        $data['sitelx'] = trim($_POST['sitelx']);
        $data['sitetcp'] = trim($_POST['sitetcp']);
        $data['sitelang'] = trim($_POST['sitelang']);
        $data['watermark'] = trim($_POST['watermark']);
        $data['watermarkimg'] = trim($_POST['watermarkimg']);
        $data['sitetpl'] = trim($_POST['sitetpl']);
        $data['waptpl'] = trim($_POST['waptpl']);
        $data['indexrec'] = trim($_POST['indexrec']);
        $data['indexhot'] = trim($_POST['indexhot']);
        $data['indexlink'] = trim($_POST['indexlink']);
        $data['indexpic'] = trim($_POST['indexpic']);
        $data['noticeid'] = trim($_POST['noticeid']);
        $data['noticenum'] = trim($_POST['noticenum']);
        $data['isping'] = trim($_POST['isping']);
        $data['pingoff'] = trim($_POST['pingoff']);
        $data['bookoff'] = trim($_POST['bookoff']);
        $data['mood'] = trim($_POST['mood']);
        $data['sitelogo'] = trim($_POST['sitelogo']);
        $data['ishits'] = trim($_POST['ishits']);
        $data['iscopyfrom'] = trim($_POST['iscopyfrom']);
        $data['isauthor'] = trim($_POST['isauthor']);
        $data['indexvote'] = trim($_POST['indexvote']);
        $data['ishomeimg'] = trim($_POST['ishomeimg']);
        $data['mouseimg'] = trim($_POST['mouseimg']);
        $data['iszy'] = trim($_POST['iszy']);
        $data['id'] = trim($_POST['id']);
        $data['artlistnum'] = trim($_POST['artlistnum']);
        $data['artlisthot'] = trim($_POST['artlisthot']);
        $data['artlistrec'] = trim($_POST['artlistrec']);
        $data['articlerec'] = trim($_POST['articlerec']);
        $data['articlehot'] = trim($_POST['articlehot']);
        $data['rollnum'] = trim($_POST['rollnum']);
        $data['postovertime'] = trim($_POST['postovertime']);
        $data['flashmode'] = trim($_POST['flashmode']);
        $data['indexnoticetitle'] = trim($_POST['indexnoticetitle']);
        $data['indexrectitle'] = trim($_POST['indexrectitle']);
        $data['indexhottitle'] = trim($_POST['indexhottitle']);
        $data['indexvotetitle'] = trim($_POST['indexvotetitle']);
        $data['indexpictitle'] = trim($_POST['indexpictitle']);
        $data['indexpicnum'] = trim($_POST['indexpicnum']);
        $data['indexpicscroll'] = trim($_POST['indexpicscroll']);
        $data['indexlinktitle'] = trim($_POST['indexlinktitle']);
        $data['indexlinkimg'] = trim($_POST['indexlinkimg']);
        $data['indexdiylink'] = trim($_POST['indexdiylink']);
        $data['listrectitle'] = trim($_POST['listrectitle']);
        $data['listhottitle'] = trim($_POST['listhottitle']);
        $data['listshowmode'] = trim($_POST['listshowmode']);
        $data['artrectitle'] = trim($_POST['artrectitle']);
        $data['arthottitle'] = trim($_POST['arthottitle']);
        $data['suffix'] = intval($_POST['suffix']);
        $data['is_build_html'] = intval($_POST['is_build_html']);
        $data['istrade'] = intval($_POST['istrade']);
        $data['isread'] = intval($_POST['isread']);
        $data['defaultup'] = intval($_POST['defaultup']);
        $data['defaultmp'] = intval($_POST['defaultmp']);
        $data['islocalpic'] = intval($_POST['islocalpic']);
        $data = array_map('remove_xss', $data);
        switch ($data['suffix']) {
            case 0:
                $resuffix = 'html';
                break;
            case 1:
                $resuffix = 'htm';
                break;
            case 2:
                $resuffix = 'shtml';
                break;
            case 3:
                $resuffix = 'php';
                break;
            case 4:
                $resuffix = 'asp';
                break;
            case 5:
                $resuffix = 'aspx';
                break;
            case 6:
                $resuffix = 'jsp';
                break;
        }
        $type = M('config', true);
        $result = $type->whereRaw('id=1')->find();
        if ($result) {
            $result->save($data);
        }
        $content = "<?php\r\nreturn ".var_export($data, true).";\r\n";
        $path = '../config/basic.php';
        $fp = fopen($path, "w") or die("<script>alert('写入配置失败，请检查'.$path.'目录是否可写入！');history.go(-1);</script>");
        fwrite($fp, $content);
        fclose($fp);
        //网站配置
        $path = '../app/home/config/app.php';
        $fp = fopen($path, "r");
        $configStr = fread($fp, filesize($path));
        fclose($fp);
        $configStr = preg_replace("/'IS_BUILD_HTML'(\s)*=>(\s)*[0-9]/", "'IS_BUILD_HTML'=>" . intval($_POST['is_build_html']), $configStr);
        $configStr = preg_replace("/'DEFAULT_THEME'(\s)*=>(\s)*'.*'/", "'DEFAULT_THEME'=>'" . htmlspecialchars($data['sitetpl'], ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'DEFAULT_WAP_THEME'(\s)*=>(\s)*'.*'/", "'DEFAULT_WAP_THEME'=>'" . htmlspecialchars($data['waptpl'], ENT_QUOTES) . "'", $configStr);
        @chmod($path, 0777);
        $fp = fopen($path, "w") or die("<script>alert('写入配置失败，请检查'.$path.'目录是否可写入！');history.go(-1);</script>");
        fwrite($fp, $configStr);
        fclose($fp);
        //伪静态后缀
        $path = '../config/route.php';
        $configStr = file_get_contents($path);
        $configStr = preg_replace("/'url_html_suffix'(\s)*=>(\s)*'.*'/", "'url_html_suffix'=>'" . $resuffix."'", $configStr);
        $fp = fopen($path, "w") or die("<script>alert('写入配置失败，请检查'.$path.'目录是否可写入！');history.go(-1);</script>");
        fwrite($fp, $configStr);
        fclose($fp);
        //保存全局配置
        $config_file = "../config/app.php";
        $fp = fopen($config_file, "r");
        $configStr = fread($fp, filesize($config_file));
        fclose($fp);
        $configStr = preg_replace("/'LOCAL_REMOTE_PIC'(\s)*=>(\s)*[0-9]/", "'LOCAL_REMOTE_PIC'=>" . $data['islocalpic'], $configStr);
        $configStr = preg_replace("/'MAIL_TRADE'(\s)*=>(\s)*[0-9]/", "'MAIL_TRADE'=>" . htmlspecialchars($_POST['MAIL_TRADE'], ENT_QUOTES) , $configStr);
        $configStr = preg_replace("/'MAIL_REG'(\s)*=>(\s)*[0-9]/", "'MAIL_REG'=>" . htmlspecialchars($_POST['MAIL_REG'], ENT_QUOTES) , $configStr);
        $configStr = preg_replace("/'MAIL_SMTP_SERVER'(\s)*=>(\s)*'.*'/", "'MAIL_SMTP_SERVER'=>'" . htmlspecialchars($_POST['MAIL_SMTP_SERVER'], ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'MAIL_FROM'(\s)*=>(\s)*'.*'/", "'MAIL_FROM'=>'" . htmlspecialchars($_POST['MAIL_FROM'], ENT_QUOTES) . "'", $configStr);
        if (config('app.MAIL_PASSSWORD') != $_POST['MAIL_PASSSWORD'] ) {
            $configStr = preg_replace("/'MAIL_PASSSWORD'(\s)*=>(\s)*'.*'/", "'MAIL_PASSSWORD'=>'" . dami_encrypt($_POST['MAIL_PASSSWORD']) . "'", $configStr);
        }
        $configStr = preg_replace("/'MAIL_TOADMIN'(\s)*=>(\s)*'.*'/", "'MAIL_TOADMIN'=>'" . htmlspecialchars($_POST['MAIL_TOADMIN'], ENT_QUOTES) . "'", $configStr);
        $configStr = preg_replace("/'MAIL_PORT'(\s)*=>(\s)*[0-9]+/", "'MAIL_PORT'=>" . intval($_POST['MAIL_PORT']) , $configStr);
        $fp = fopen($config_file, "w") or die("<script>alert('写入配置失败，请检查'.$config_file.'是否可写入！');history.go(-1);</script>");
        fwrite($fp, $configStr);
        fclose($fp);
        //清理缓存
        $runtime_path = '../runtime/';
        if (is_dir($runtime_path)) {
            @deldir($runtime_path);
        }
        $this->assign("jumpUrl", U('Config/index'));
        $this->success('操作成功!');

    }
}

?>