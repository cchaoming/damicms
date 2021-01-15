<?php
/***********************************************************
 * [大米CMS] (C)2011 - 2011 damicms.com
 *
 * @function 前台函数库
 *
 * @Filename common.class.php $
 *
 * @Author 追影 QQ:279197963 $
 *
 * @Date 2011-11-23 14:56:54 $
 *************************************************************/
/**
 * +----------------------------------------------------------
 * 取得label表内容
 * 示例:{:label(1);}
 * +----------------------------------------------------------
 * @access label
 * +----------------------------------------------------------
 * @param int $id 编号
 * +----------------------------------------------------------
 */
function label($id)
{
    $label = \think\facade\Db::name('label');
    $data = $label->whereRaw('id=' . $id . " AND status=1")->field('content')->find();
    if ($data) {
        return $data['content'];
    }
}

/**
 * +----------------------------------------------------------
 * 取得article表内容:热门/置顶/推荐/图文
 * 示例:{:ShowArt([0],[10],[1],[0]);} [] 代表参数可省略
 * +----------------------------------------------------------
 * @access article
 * +----------------------------------------------------------
 * @param int $num 开始位置[缺省为0]
 * @param int $num2 结束位置[缺省为10]
 * @param int $target 打开方式:0:原窗口(默认),1:新窗口
 * @param int $max 0:热门(orderby hits)1:置顶(istop),2:推荐(ishot),3图文(isimg)
 * @param int $typeid ：栏目ID
 * +--------------------------------------------------------------------
 */
function ShowArt($num, $num2, $target, $conditions, $typeid = 0)
{
    $article = \think\facade\Db::name('article');
    $map[] = ['status', '=', 1];
    if ($typeid != 0) {
        $arr = get_tree($typeid);
        $map[] = ['typeid', 'in', $arr];
    }
    if (!isset($target) or $target == 0) {
        $tar = '';
    } else {
        $tar = "target=\"_blank\"";
    }
    if (!isset($num)) $num = 0;
    if (!isset($num2)) $num = 10;
    switch ($conditions) {
        case 0:
            $field = '*';
            $data = 'hits desc';
            break;
        case 1:
            $map[] = ['istop', '=', 1];//置顶
            $field = '*';
            $data = 'addtime desc';
            break;
        case 2:
            $map[] = ['ishot', '=', 1];//推荐
            $field = '*';
            $data = 'addtime desc';
            break;
        case 3:
            $map[] = ['isimg', '=', 1];//图文
            $field = '*';
            $data = 'addtime desc';
            break;
        default:
            $map[] = ['isimg', '=', 1];//图文
            $field = '*';
            $data = '';

    }
    $list = $article->where($map)->field($field)->orderRaw($data)->limit($num . ',' . $num2)->select()->toArray();
    //释放内存
    unset($map, $field, $num, $num2, $article);
    if (!$list) {
        return '没有文章';
        exit;
    }
    $html = '';
    $img = '';
    foreach ($list as $v) {
        if ($conditions == 3) {
            $img = "<img src=\"__ROOT__{$v['imgurl']}\" width=\"160px\" height=\"126px\"><br>";
        }
        $url = U('articles/' . $v['aid']);
        $html .= "\n<li><a href=\"{$url}\" {$tar}>{$img}{$v['title']}</a></li>";
    }
    //释放内存
    unset($list);
    return $html;
}



//正则提取图片
function get_imagesarray($str)
{
    preg_match_all('/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/i', $str, $match);
    if (count($match) > 0) {
        return $match[1];
    } else {
        return false;
    }
}

//是否有子孙
function have_child($typeid)
{
    $t = \think\facade\Db::name('type')->whereRaw('fid=' . $typeid)->find();
    return $t ? true : false;
}

//获取我的家族成员(单边)
function get_tree($typeid)
{
    $str = '';
    $dao = \think\facade\Db::name('type');
    $t = $dao->whereRaw('typeid =' . $typeid)->find();
    if ($t) {
        $arr = explode('-', $t["path"]);
        if ($arr[0] == 0) {
            array_shift($arr);
        }
        $str = join(',', $arr);
        if ($str != '') {
            $str .= ',';
        }
        $str .= get_children($typeid);
    }
    return $str;
}

//获取我的家族全部成员
function get_all_tree($typeid)
{
    $str = '';
    $dao = \think\facade\Db::name('type');
    $t = $dao->whereRaw('typeid =' . $typeid)->find();
    if ($t) {
        $arr = explode('-', $t["path"]);
        if (count($arr) == 1) {
            $str = get_children($typeid);
        } else {
            $str = get_children($arr[1]);
        }
    }
    return $str;
}

//获取我的始祖失败返回-1
function get_first_father($typeid, $i = 1)
{
    $t = \think\facade\Db::name('type')->whereRaw('typeid =' . $typeid)->find();
    if ($t) {
        if ($t['fid'] == 0) {
            return $typeid;
        } else {
            $arr = explode("-", $t["path"]);
            if (count($arr) >= 2) {
                return $arr[$i];
            } else {
                return -1;
            }
        }
    } else {
        return -1;
    }
}

/**
 * +----------------------------------------------------------
 * 取得顶级栏目名称
 *
 * +----------------------------------------------------------
 * @param char
 * +----------------------------------------------------------
 * @author zerowang
 * +----------------------------------------------------------
 */
function get_first_sortName($typeid)
{
    $firstid = get_first_father($typeid);
    $rs = \think\facade\Db::name('type')->whereRaw('typeid =' . $firstid)->find();
    return $rs["typename"];
}

/**
 * +----------------------------------------------------------
 * 取得Ad表内容
 * 示例:{:Ad(1);}
 * +----------------------------------------------------------
 * @access ad
 * +----------------------------------------------------------
 * @param int $id 编号
 * +----------------------------------------------------------
 */
function Ad($id)
{
    $ad = \think\facade\Db::name('ad');
    $data[] = ['id','=',$id];
    $data[] = ['status','=',1];
    $content = $ad->where($data)->value('content');
    unset($ad);
    return $content;
}

/**
 * +----------------------------------------------------------
 * 取得article表内容 通常用于栏目列表页
 * 示例:{:lists(1,0,'list');} [] 代表可缺省
 * +----------------------------------------------------------
 * @access article,type
 * +----------------------------------------------------------
 * @param int $typeid 栏目id
 * @param int $mode 查询模式
 * 0:查询子栏目和本栏目 1:仅查询本栏目 2:仅查询子栏目
 * @param int/string $limit 取得数据的条数
 * 可以是数字:10,代表查询前10条
 * 可以是字符串:"'1,10'",代表从第1条取到第10条
 * @param string $param 写入模板函数名 如'list'
 * +----------------------------------------------------------
 */
function lists($typeid, $mode, $limit, $param, $order = 'addtime desc')
{
    //查询数据库
    $article = \think\facade\Db::view('Article');
    //封装条件
    $map[] = ['status','=',1];
    $arr = get_children($typeid);
    //模式判断
    switch ($mode) {
        case 0:
            $map[] = ['Article.typeid','in', $arr];
            break;
        case 1:
            $map[] = ['Article.typeid','=',$typeid];
            break;
        case 2:
            $map[] = ['Article.typeid','in', $arr];
            break;
    }
    $alist = $article->view('Type','typename')->where($map)->orderRaw($order)->limit($limit)->select()->toArray();
    //封装变量
    return $alist;
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

//时间比较
function cptime($time1, $time2)
{
    if (strtotime($time1) > strtotime($time2)) {
        return true;
    } else {
        return false;
    }
}

//高亮搜索关键字
function highlight_keyword(&$val, $key, $light)
{
    $val = preg_replace("/{$light}/i", "<font color=red><b>{$light}</b></font>", $val);
}

/**
 * +----------------------------------------------------------
 *自定义模板常量路径转换
 * ACTION里示例:Header('Location:'.turl($list['url']));
 * +----------------------------------------------------------
 * @access null
 * +----------------------------------------------------------
 * @param string $str 需转换的url;将自定义模板路径转换
 * +----------------------------------------------------------
 **/
function turl($str)
{
    $article = "__ARTICLE__";
    $type = "__TYPE__";
    $web = "__WEB__";
    $vote = "__VOTE__";
    $rearticle = __ROOT__ . "/index.php?s=articles";
    $retype = __ROOT__ . "/index.php?s=lists";
    $reweb = __ROOT__ . "/index.php?s=";
    $revote = __ROOT__ . "/index.php?s=votes";
    $str = str_replace($article, $rearticle, $str);
    $str = str_replace($type, $retype, $str);
    $str = str_replace($web, $reweb, $str);
    $str = str_replace($vote, $revote, $str);
    return $str;
}

//获取目录下所有HTML文件
function get_files($dir)
{
    $ret = array();
    $handler = opendir($dir);
    while (($filename = readdir($handler)) !== false) {
        if (stripos($filename, '.html') !== false) {
            $ret[] = $filename;
        }
    }
    closedir($handler);
    return $ret;
}

//图片返回
function ret_pic($url, $default_pic = "/public/image/nopic.gif")
{
    if (strlen($url) > 3) {
        return $url;
    } else {
        return $default_pic;
    }
}

//生成缩略图返回网站绝对路径
function thumb_pic($source, $width, $height)
{
    require_once('../extend/Image.class.php');
    $path_str = substr($source, 0, 1) == '/' ? '.' . $source : $source;
    $thumbPath = substr($path_str, 0, strripos($path_str, '/') + 1);
    $filename = substr($path_str, strripos($path_str, '/') + 1);
    $file_ext = substr($filename, strripos($filename, '.'));
    $file_pre = substr($filename, 0, strripos($filename, '.'));
    $thumbname = $file_pre . "_{$width}_{$height}" . $file_ext;
    if (!file_exists($thumbPath . $thumbname)) {
        Image::thumb($path_str, $thumbPath . $thumbname, '', $width, $height, true);
    }
    $ret = substr($thumbPath . $thumbname, 1);
    return $ret;
}

//过滤xss
function loopxss(array $array)
{
    foreach ($array as $k => $v) {
        if (is_string($v)) {
            $array[$k] = remove_xss($v);
        } else if (is_array($v)) {
            $array[$k] = loopxss($v);
        }
    }
    return $array;
}

//POST请求
function SPost($curlPost, $url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
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

//发送验证码
function send_smsmess($to_mobile, $content, $isvail = 0)
{
    $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
    if ($isvail == 1) {
        $config = config('basic');
        $mobile_code = mt_rand(100000, 999999);
        $content = "您的验证码是：" . $mobile_code . "。请不要把验证码泄露给其他人。";
    }
    $data = ("account=" .config('app.SMS_USER') . "&password=" . config('app.SMS_PWD') . "&mobile=" . $to_mobile . "&content=" . rawurlencode($content));//短信用户名与密码请在这里改
//密码可以使用明文密码或使用32位MD5加密
    $gets = xml_to_array(SPost($data, $target));
    //var_dump($gets);
    if ($gets['SubmitResult']['code'] == 2 && $isvail == 1) {
        session('mobile_verify',md5($mobile_code));
    }
    return $gets['SubmitResult']['code'];
}

function geturl($model,$id){
    if($model=='lists'){
        $cache_url = S('url_'.$id);
        if($cache_url){
            return $cache_url;
        }
        $url = get_field('type','typeid='.intval($id) . ' and islink=1','url');
        if($url != ''){
            S('url_'.$id,$url);
            return $url;
        }
    }
    return U($model.'/'.$id);
}
?>