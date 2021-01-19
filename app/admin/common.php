<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 后台函数库
 *
 * @Filename common.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-14 08:45:20 $
 *************************************************************/
//删除目录函数
function deldir($dirname)
{
    if (file_exists($dirname)) {
        $dir = opendir($dirname);
        while ($filename = readdir($dir)) {
            if ($filename != "." && $filename != "..") {
                $file = $dirname . "/" . $filename;
                if (is_dir($file)) {
                    deldir($file); //使用递归删除子目录
                } else {
                    @unlink($file);
                }
            }
        }
        closedir($dir);
        rmdir($dirname);
    }
}

/*-----文件夹与文件操作函数-----*/
//读取文件
function read_file($l1)
{
    return @file_get_contents($l1);
}

//写入文件
function write_file($l1, $l2 = '')
{
    $dir = dirname($l1);
    clearstatcache();
    if (!is_dir($dir)) {
        mkdirss($dir);
    }
    return @file_put_contents($l1, $l2);
}

//递归创建文件
function mkdirss($dirs, $mode = 0777)
{
    clearstatcache();
    if (!is_dir($dirs)) {
        mkdirss(dirname($dirs), $mode);
        return @mkdir($dirs, $mode);
    }
    return true;
}

// 数组保存到文件
function arr2file($filename, $arr = '')
{
    if (is_array($arr)) {
        $con = var_export($arr, true);
    } else {
        $con = $arr;
    }
    $con = "<?php\nreturn $con;\n?>";
    write_file($filename, $con);
}

// 获取文件夹大小
function getdirsize($dir)
{
    $dirlist = opendir($dir);
    $dirsize = 0;
    while (false !== ($folderorfile = readdir($dirlist))) {
        if ($folderorfile != "." && $folderorfile != "..") {
            if (is_dir("$dir/$folderorfile")) {
                $dirsize += getdirsize("$dir/$folderorfile");
            } else {
                $dirsize += filesize("$dir/$folderorfile");
            }
        }
    }
    closedir($dirlist);
    return $dirsize;
}

//替换采集等通过url参数传值
function dami_url_repalce($xmlurl, $order = 'asc')
{
    if ($order == 'asc') {
        return str_replace(array('|', '@', '#'), array('/', '=', '&'), $xmlurl);
    } else {
        return str_replace(array('/', '=', '&'), array('|', '@', '#'), $xmlurl);
    }
}

/**
 * +----------------------------------------------------------
 * 对查询结果集进行排序
 * +----------------------------------------------------------
 * @access public
 * +----------------------------------------------------------
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * +----------------------------------------------------------
 * @return array
 * +----------------------------------------------------------
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }
        return $resultSet;
    }
    return false;
}

// 获取时间颜色:24小时内为红色
function getcolordate($type = 'Y-m-d H:i:s', $time, $color = 'red')
{
    if(!is_numeric($time)){$time=0;}
    if ((time() - $time) > 86400) {
        return date($type, $time);
    } else {
        return '<font color="' . $color . '">' . date($type, $time) . '</font>';
    }
}

/**
 * +----------------------------------------------------------
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 * +----------------------------------------------------------
 * @return string
 * +----------------------------------------------------------
 */
function byte_format($size, $dec = 2)
{
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size, $dec) . " " . $a[$pos];
}

//测试写入文件
function testwrite($d)
{
    $tfile = '_wk.txt';
    $d = preg_replace('/\/$/', '', $d);
    $fp = @fopen($d . '/' . $tfile, 'w');
    if (!$fp) {
        return false;
    } else {
        fclose($fp);
        $rs = @unlink($d . '/' . $tfile);
        if ($rs) {
            return true;
        } else {
            return false;
        }
    }
}

//解密函数 for utf-8
function unescape($str)
{
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        if ($str[$i] == '%' && $str[$i + 1] == 'u') {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f) {
                $ret .= chr($val);
            } else if ($val < 0x800) {
                $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
            } else {
                $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
            }
            $i += 5;
        } else if ($str[$i] == '%') {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        } else {
            $ret .= $str[$i];
        }
    }
    return $ret;
}

//获取模板类型名称
function gettplname($filename)
{
    switch ($filename) {
        case 'index.html':
            return '网站首页模板';
            break;
        case 'footer.html':
            return '网站底部模板';
            break;
        case 'head.html':
            return '网站头部模板';
            break;
        case 'search.html':
            return '搜索页模板';
            break;
        case 'article_article.html':
            return '文章模型文章页';
            break;
        case 'article_list.html':
            return '文章模型列表页';
            break;
        case 'guestbook.html':
            return '留言本模板';
            break;
        case 'guestbook_nopl.html':
            return '留言本空白xml';
            break;
        case 'guestbook_pl.html':
            return '留言本xml';
            break;
        case 'pl_pl.html':
            return '评论页xml';
            break;
        case 'pl_nopl.html':
            return '评论页空白xml';
            break;
        case 'guestbook_pl.html':
            return '留言本xml';
            break;
        case 'vote.html':
            return '投票页模板';
            break;
    }
    $f = explode('.', $filename);
    switch ($f[1]) {
        case 'js':
            return 'js脚本文件';
            break;
        case 'php':
            return 'php脚本文件';
            break;
        case 'css':
            return '层叠样式表';
            break;
        case 'jpg':
            return 'jpg图片';
            break;
        case 'gif':
            return 'gif图片';
            break;
        case 'png':
            return 'png图片';
            break;
        case 'zip':
            return 'zip压缩包';
            break;
        case 'rar':
            return 'rar压缩包';
            break;
        case 'html':
            return '模板文件';
            break;
        case 'htm':
            return '网页文件';
            break;
        case 'ico':
            return 'ico图标';
            break;
        case 'wmv':
            return 'wmv视频文件';
            break;
        case 'swf':
            return 'flash文件';
            break;
        case 'wma':
            return 'wma音频文件';
            break;
        case 'mp3':
            return 'mp3音频文件';
            break;
        case 'flv':
            return 'flv视频文件';
            break;
        case 'mp4':
            return 'mp4视频文件';
            break;
        default:
            return '未知文件';
            break;
    }
}

function show_field($type, $name, $value = "", $option = "", $css = "")
{
    switch ($type) {
        case 0: //单行文本
            echo "<input type=\"text\" name=\"{$name}\" id=\"{$name}\" value=\"";
            zy_php_value($value);
            echo "\" {$css} />";
            break;
        case 1: //多行文本
            echo "<textarea name=\"{$name}\" {$css}>";
            zy_php_value($value);
            echo "</textarea>";
            break;
        case 2://html编辑器
            echo "<script>KindEditor.ready(function(K) {K.create(\"#{$name}\",{ afterBlur: function(){ this.sync(); }});});</script><textarea id=\"{$name}\" name=\"{$name}\" style=\"width:700px;height:300px;\" {$css}>";
            zy_php_value($value);
            echo "</textarea>";
            break;
        case 3://单选下拉列表
            if ($option != '') {
                echo "<select name=\"{$name}\" id=\"{$name}\" {$css}>";
                if (preg_match("/<php>(.*)<\/php>/", $option, $temp)) {
                    eval($temp[1]);
                } else {
                    $arr = explode(",", $option);
                    for ($i = 0; $i < count($arr); $i++) {
                        $select = '';
                        $temp = explode("|", $arr[$i]);
                        if (count($temp) == 2) {
                            if ($temp[1] == $value) {
                                $select = 'selected="selected"';
                            }
                            echo "<option value=\"" . $temp[1] . "\" $select>" . $temp[0] . "</option>";
                        } else if (count($temp) == 1) {
                            if ($temp[0] == $value) {
                                $select = 'selected="selected"';
                            }
                            echo "<option value=\"" . $temp[0] . "\" $select>" . $temp[0] . "</option>";
                        }
                    }
                }
                echo "</select><script>$('#{$name}').val('{$value}');</script>";
            }
            break;
        case 4://单选按钮
            if ($option != '') {

                $arr = explode(",", $option);
                for ($i = 0; $i < count($arr); $i++) {
                    $select = '';
                    $temp = explode("|", $arr[$i]);
                    if (count($temp) == 2) {
                        if ($temp[1] == $value) {
                            $select = 'checked="checked"';
                        }
                        echo "<input name=\"{$name}\" type=\"radio\" value=\"" . $temp[1] . "\" $select {$css}/>" . $temp[0] . "&nbsp;";
                    } else if (count($temp) == 1) {
                        if ($temp[0] == $value) {
                            $select = 'checked="checked"';
                        }
                        echo "<input name=\"{$name}\" type=\"radio\" value=\"" . $temp[0] . "\" $select {$css}/>" . $temp[0] . "&nbsp;";
                    }
                }

            }
            break;
        case 5://多选列表
            if ($option != '') {
                echo "<select name=\"{$name}[]\" size=\"4\" multiple=\"multiple\" {$css}>";
                if (preg_match("/<php>(.*)<\/php>/", $option, $temp)) {
                    eval($temp[1]);
                } else {
                    $arr = explode(",", $option);
                    $value_arr = explode(",", $value);
                    for ($i = 0; $i < count($arr); $i++) {
                        $select = '';
                        $temp = explode("|", $arr[$i]);
                        if (count($temp) == 2) {
                            if (in_array($temp[1], $value_arr)) {
                                $select = 'selected="selected"';
                            }
                            echo "<option value=\"" . $temp[1] . "\" $select>" . $temp[0] . "</option>";
                        } else if (count($temp) == 1) {
                            if (in_array($temp[0], $value_arr)) {
                                $select = 'selected="selected"';
                            }
                            echo "<option value=\"" . $temp[0] . "\" $select>" . $temp[0] . "</option>";
                        }
                    }
                }
                echo "</select><script>$('#{$name}').val([{$value}]);</script>";
            }
            break;
        case 6://多选按钮checkbox
            if ($option != '') {
                $arr = explode(",", $option);
                $value_arr = explode(",", $value);
                for ($i = 0; $i < count($arr); $i++) {
                    $select = '';
                    $temp = explode("|", $arr[$i]);
                    if (count($temp) == 2) {
                        if (in_array($temp[1], $value_arr)) {
                            $select = 'checked="checked"';
                        }
                        echo "<input name=\"{$name}[]\" type=\"checkbox\" value=\"" . $temp[1] . "\" $select {$css}/>" . $temp[0] . "&nbsp;";
                    } else if (count($temp) == 1) {
                        if (in_array($temp[0], $value_arr)) {
                            $select = 'checked="checked"';
                        }
                        echo "<input name=\"{$name}[]\" type=\"checkbox\" value=\"" . $temp[0] . "\" $select {$css}/>" . $temp[0] . "&nbsp;";
                    }
                }
            }
            break;
        case 7://文件上传
            echo "<script>KindEditor.ready(function(K){var editor = K.editor({allowFileManager : true});K(\"#insertfile_{$name}\").click(function() {					editor.loadPlugin(\"insertfile\", function() {editor.plugin.fileDialog({fileUrl : K(\"#{$name}\").val(),clickFn : function(url, title) {								K(\"#{$name}\").val(url);editor.hideDialog();}});});});});</script><input type=\"text\" id=\"{$name}\" name=\"{$name}\" value=\"";
            zy_php_value($value);
            echo "\" {$css}/> <input type=\"button\" id=\"insertfile_{$name}\" value=\"选择文件\" />";
            break;
        case 8:
            echo "<script>KindEditor.ready(function(K){var editor = K.editor({allowFileManager : true});K(\"#{$name}_btn\").click(function() {editor.loadPlugin(\"multifile\", function() {editor.plugin.multiFileDialog({clickFn : function(urlList) {console.log(urlList);var div = K('#{$name}');var temp='';K.each(urlList, function (i, data) {if(data.url){temp = (temp + data.url + ';');}});ret=temp.substring(0,temp.length-1);var old_value=div.val();if(old_value!=''){if(ret!=''){ret = old_value + ';' + ret;}else{ret = old_value;}} div.val(ret);editor.hideDialog();load_picyulan(ret,'{$name}'); }});});});});</script><input type=\"text\" id=\"{$name}\" name=\"{$name}\" value=\"";
            zy_php_value($value);
            echo "\" {$css}/> <input type=\"button\" id=\"{$name}_btn\" value=\"选择上传\" /><script>if($('#{$name}').val() != ''){load_picyulan($('#{$name}').val(),'{$name}');}</script>";
            break;
    }
}

//让其支持PHP代码
function zy_php_value($v)
{
    preg_match("/<php>(.*)<\/php>/i", str_replace(PHP_EOL, '', $v), $temp);
    if (count($temp) >= 1) {
        eval($temp[1]);
    } else {
        echo $v;
    }
}

//栏目的扩展字段
function list_extend_field($typeid)
{
    $prefix = (string)config('database.connections.mysql.prefix');
    $extend_field = M('extend_show')->alias('extend_show')->join($prefix. 'extend_fieldes extend_fieldes', 'extend_show.field_id=extend_fieldes.field_id')->whereRaw('extend_show.is_show=1 and extend_show.typeid=' . $typeid)->field('extend_fieldes.*')->orderRaw('extend_fieldes.orders asc')->select()->toArray();
    //echo M('extend_show')->getLastSql();
    return $extend_field;
}

//获取我以及子孙返回字符串10,45,478
function get_childrenget_children($typeid, $withself = 1, $ret = 0)
{
    $temp = array();
    if ($withself == 1) {
        $temp[] = $typeid;
    }
    $dao = M('type');
    $t = $dao->whereRaw('typeid =' . (int)$typeid)->find();
    if ($t) {
        $str = $t["path"] . "-" . $t["typeid"];
        $mylist = $dao->removeOption()->whereRaw("1 = instr(path,'" . $str . "')")->select()->toArray();
        foreach ($mylist as $kk => $vv) {
            $temp[] = $vv['typeid'];
        }
    }
    return $ret == 0 ? join(',', $temp) : $temp;
}

//获取子孙城市
function get_childcity($typeid, $withself = 1)
{
    $temp = array();
    if ($withself == 1) {
        $temp[] = $typeid;
    }
    $dao = M('city');
    $t = $dao->whereRaw('city_id =' . $typeid)->find();
    if ($t) {
        $str = $t["path"] . "-" . $t["city_id"];
        $mylist = $dao->removeOption()->whereRaw("1 = instr(path,'" . $str . "')")->select()->toArray();
        foreach ($mylist as $kk => $vv) {
            $temp[] = $vv['city_id'];
        }
    }
    return join(',', $temp);
}

//递归处理stripslashes
function stripslashesRecursive(array $array)
{
    foreach ($array as $k => $v) {
        if (is_string($v)) {
            $array[$k] = stripslashes($v);
        } else if (is_array($v)) {
            $array[$k] = stripslashesRecursive($v);
        }
    }
    return $array;
}

function color_red($str)
{
    $error_arr = array('错误', '失败', '限制', '警告');
    if (in_array($str, $error_arr)) {
        return '<font color="#ff0000">' . $str . '</font>';
    }
    return $str;
}

?>