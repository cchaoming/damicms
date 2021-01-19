<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 清理附件与缓存
 *
 * @Filename ClearAction.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-17 14:56:44 $
 *************************************************************/

namespace app\admin\controller;
class Clear extends Common
{
    //清理入口
    public function index()
    {
        return $this->display('index');
    }

    //****清理幻灯附件*****
    public function clearhd()
    {
        //统计附件
        $path = './Public/Uploads/hd/';
        $fso = opendir($path);
        if (!$fso) {
            $this->error('系统幻灯文件夹不存在!');

        }
        while ($flist = readdir($fso)) {
            $data[] = $flist;
        }
        closedir($fso);
        $data = array_slice($data, 2);

        //读取数据库附件内容
        $a = M('flash');
        $list = $a->field('pic')->select()->toArray();
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $pic[] = $v['pic'];
            }
            //取得差集附件
            $dpic = array_diff($data, $pic);
        } else {
            $dpic = $data;
        }

        if (empty($dpic)) {
            $msg = '恭喜,系统无幻灯垃圾文件!';
            $this->assign('waitSecond', 10);
            $this->success($msg);

        }
        $i = 0;
        $j = 0;
        $p = '';
        //执行清理
        foreach ($dpic as $v) {
            $ipath = $path . $v;
            $result = @unlink($ipath);
            if ($result == true) {
                $i++;//统计成功删除的文件个数
            } else {
                $j++;//统计未删除的文件个数
                $p .= $v . ',&nbsp;&nbsp;';
            }
        }
        $msg = '系统存在垃圾幻灯附件' . ($i + $j) . "个<br>成功清理了{$i}个文件";
        if ($j > 0) {
            $msg .= "其中有{$j}个文件不能清理:<br>" . $p;
        }
        $this->assign('waitSecond', 10);
        $this->success($msg);
    }

    //****清理友情链接****
    public function clearlink()
    {
        //统计附件
        $path = './Public/Uploads/link/';
        $fso = opendir($path);
        if (!$fso) {
            $this->error('系统友情链接文件夹不存在!');

        }
        while ($flist = readdir($fso)) {
            $data[] = $flist;
        }
        closedir($fso);
        $data = array_slice($data, 2);

        //读取数据库附件内容
        $a = M('link');
        $list = $a->field('logo')->select()->toArray();

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $pic[] = $v['logo'];
            }
            //取得差集附件
            $dpic = array_diff($data, $pic);
        } else {
            $dpic = $data;
        }
        if (empty($dpic)) {
            $msg = '恭喜,系统无友链垃圾文件!';
            $this->assign("waitSecond", 10);
            $this->success($msg);

        }
        $i = 0;
        $j = 0;
        $p = '';
        //执行清理
        foreach ($dpic as $v) {
            $ipath = $path . $v;
            $result = @unlink($ipath);

            if ($result == true) {
                $i++;//统计成功删除的文件个数
            } else {
                $j++;//统计未删除的文件个数
                $p .= $v . ',&nbsp;&nbsp;';
            }
        }
        $msg = '系统存在友链垃圾附件' . ($i + $j) . "个<br>成功清理了{$i}个文件";
        if ($j > 0) {
            $msg .= "其中有{$j}个文件不能清理:<br>" . $p;
        }
        $this->assign('waitSecond', 10);
        $this->success($msg);
    }

    //****清理LOGO附件****
    public function clearlogo()
    {
        //统计附件
        $path = './Public/Uploads/logo/';
        $fso = opendir($path);
        if (!$fso) {
            $this->error('系统LOGO文件夹不存在!');

        }
        while ($flist = readdir($fso)) {
            $data[] = $flist;
        }
        closedir($fso);
        $data = array_slice($data, 2);
        //读取数据库附件内容
        $a = M('config');
        $logo = $a->whereRaw('id=1')->value('sitelogo');
        $watermarkimg = $a->value('watermarkimg');
        if (!empty($logo) || !empty($watermarkimg)) {
            if (!empty($logo)) $pic[] = $logo;
            if (!empty($watermarkimg)) $pic[] = $watermarkimg;
            //取得差集附件
            $dpic = array_diff($data, $pic);
        } else {
            $dpic = $data;
        }
        if (empty($dpic)) {
            $msg = '恭喜,系统无logo垃圾文件!';
            $this->assign('waitSecond', 10);
            $this->success($msg);

        }
        $i = 0;
        $j = 0;
        $p = '';
        //执行清理
        foreach ($dpic as $v) {
            $ipath = $path . $v;
            $result = @unlink($ipath);

            if ($result) {
                $i++;//统计成功删除的文件个数
            } else {
                $j++;//统计未删除的文件个数
                $p .= $v . ',&nbsp;&nbsp;';
            }
        }

        $msg = '系统存在垃圾logo附件' . ($i + $j) . "个<br>成功清理了{$i}个文件";
        if ($j > 0) {
            $msg .= "其中有{$j}个文件不能清理:<br>" . $p;
        }
        $this->assign('waitSecond', 10);
        $this->success($msg);
    }

    //****清理系统缓存****
    public function clearcache()
    {
        //缓存路径
        $Webpath = '../runtime/';
        clearstatcache();
        if (is_dir($Webpath)) {
            @deldir($Webpath);
        }
        $msg = '系统缓存清理完毕!';
        $this->assign('waitSecond', 10);
        $this->assign('jumpUrl', U('Index/main'));
        $this->success($msg);
    }
}

?>