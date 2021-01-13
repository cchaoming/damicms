<?php
// 应用公共文件
use think\exception\ClassNotFoundException;

function get_back_url()
{
    if (isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER["HTTP_REFERER"])) {
        $queryStr = explode('?', $_SERVER["HTTP_REFERER"]);
        if (count($queryStr) == 2) {
            parse_str($queryStr[1], $queryArr);
            if (isset($queryArr['back_url']) && !empty($queryArr['back_url'])) {
                $backUrl = explode("&", urldecode($queryArr['back_url']));
                foreach ($backUrl as $k => $v) {
                    $v = explode("=", $v);
                    if (isset($v[1]) && !empty($v[1])) {
                        $backArr[$v[0]] = $v[1];
                    }
                }
            }
        }
    }
    return $backArr ?? [];
}

//获取某一字段
function get_field($table, $where, $field, $cache=false,$with_prefix = true)
{
    $db_model = $with_prefix ? \think\facade\Db::name($table) : \think\facade\Db::table($table);
    $t = $db_model->whereRaw($where)->cache($cache)->value($field);
    //echo $db_model->getLastSql();
    return $t;
}

//防止xss
function remove_xss($val)
{
    if($val != strip_tags($val)){
        return remove_html_xss($val);
    }else{
        return htmlspecialchars($val,ENT_QUOTES);
    }
}

function remove_html_xss($string){

    require_once '../extend/htmlpurifier/library/HTMLPurifier.auto.php';
    // 生成配置对象
    $_clean_xss_config = HTMLPurifier_Config::createDefault();
    // 以下就是配置：
    $_clean_xss_config->set('Core.Encoding', 'UTF-8');
    // 设置允许使用的HTML标签
    $_clean_xss_config->set('HTML.Allowed','div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src],video[autoplay|controls|height|loop|src|width]');
    // 设置允许出现的CSS样式属性
    $_clean_xss_config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
    // 设置a标签上是否允许使用target="_blank"
    $_clean_xss_config->set('HTML.TargetBlank', TRUE);
    // 使用配置生成过滤用的对象
    $_clean_xss_obj = new HTMLPurifier($_clean_xss_config);
    // 过滤字符串
    return $_clean_xss_obj->purify($string);
}

//防止sql注入
function inject_check($str)
{
    $str=urldecode($str);
    $tmp = preg_match('/select(\s|"|\'|`)+|insert(\s|"|\'|`)+|update(\s|"|\'|`)+|and(\s|"|\'|`)+|or(\s|"|\'|`)+|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into(\s|"|\'|`)+|load_file|outfile/is', $str);
    if ($tmp) {
        alert("非法操作,请联系管理员!");
    } else {
        return $str;
    }
}

/**
 * +----------------------------------------------------------
 *javascript 弹窗信息
 * Action示例:alert('操作失败!',1);
 * +----------------------------------------------------------
 * @access null
 * +----------------------------------------------------------
 * @param string $msg 弹窗信息
 * @param int $url 跳转url
 * +----------------------------------------------------------
 **/
function alert($msg, $url = null)
{
    header('Content-type: text/html; charset=utf-8');
    $str = "<script language=\"javascript\">";
    $str .= "alert('{$msg}');";
    if($url ==3){
        $s = "self.close();";
    }else if(is_string($url)){
        $s = "location.href='{$url}';";
    }else{
        $s = "window.history.go(-1);";
    }
    $str .= $s;
    $str .= "</script>";
    exit($str);
}

/*获取远程url内容*/
function get_url_contents($url,$data = [])
{
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        if($data){
            curl_setopt($ch, CURLOPT_POST, 1); // 发送一个常规的Post请求
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Post提交的数据包
        }
        $result = curl_exec($ch);
        curl_close($ch);
        curl_close($ch);
        curl_close($ch);
        return $result;
    } else {
        if (ini_get("allow_url_fopen") == "1") {
            return file_get_contents($url);
        }
    }
}
//手机端检测
function check_wap()
{
    if (!empty($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {// 先检查是否为wap代理，准确度高
        return true;
    } elseif (strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML") > 0) {// 检查浏览器是否接受 WML.
        return true;
    } elseif (preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {//检查USER_AGENT
        return true;
    }
    elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    } else {
        return false;
    }
}
//发送邮件
function send_mail($sendto_email, $user_name, $subject, $bodyurl, $port = 25)
{
    require_once '../extend/phpmailer/PhpMailer.php';
    $mail = new PHPMailer();
    $mail->IsSMTP();// send via SMTP
    $mail->SMTPDebug = 0; // 启用SMTP调试功能
    $mail->Host = config('app.MAIL_SMTP_SERVER');   // SMTP servers
    $mail->Port = $port;
    $mail->SMTPAuth = true;           // turn on SMTP authentication
    $mail->Username = config('app.MAIL_FROM');     // SMTP username  注意：普通邮件认证不需要加 @域名
    $mail->Password = dami_decrypt(config('app.MAIL_PASSSWORD')); // SMTP password
    $mail->From = config('app.MAIL_FROM');      // 发件人邮箱
    $mail->FromName = config('app.MAIL_FROM');  // 发件人

    $mail->CharSet = "utf-8";   // 这里指定字符集！
    $mail->Encoding = "base64";
    $mail->AddAddress($sendto_email, $user_name);  // 收件人邮箱和姓名
    //$mail->SetFrom(C('MAIL_FROM'),C('sitetitle'));
    //$mail->AddReplyTo(C('MAIL_FROM'), C('MAIL_FROMNAME'));
    $mail->IsHTML(true);  // send as HTML
    // 邮件主题
    $mail->Subject = $subject;
    // 邮件内容
    $mail->Body = '<html><head><meta http-equiv="Content-Language" content="zh-cn"/><meta http-equiv="Content-Type" content="text/html;charset=utf-8"/></head><body><div style="width:80%;padding:30px 20px;margin:0 auto;"><span style="font-weight:bold;font-size:16px;">尊敬的' . $user_name . '</span><br/><br/>' . $bodyurl . '<br/></div></body></html>';
    $mail->AltBody = "text/html";
    if (!$mail->Send()) {
        return false;
    } else {
        return true;
    }
}

function dami_encrypt($data, $key = 'echounion')
{
    $key = md5($key);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= $key{$x};
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
    }
    return base64_encode($str);
}

function dami_decrypt($data, $key = 'echounion')
{
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return $str;
}

//将 xml数据转换为数组格式。
function xml_to_array($xml){
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches)){
        $count = count($matches[0]);
        for($i = 0; $i < $count; $i++){
            $subxml= $matches[2][$i];
            $key = $matches[1][$i];
            if(preg_match( $reg, $subxml )){
                $arr[$key] = xml_to_array( $subxml );
            }else{
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}

// 循环创建目录
function mk_dir($dir, $mode = 0777)
{
    if (is_dir($dir) || @mkdir($dir, $mode))
        return true;
    if (!mk_dir(dirname($dir), $mode))
        return false;
    return @mkdir($dir, $mode);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = false)
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

//html无损裁剪
function htmlsubstr($str, $num, $more = false)
{
    $leng = strlen($str);
    if ($num >= $leng)
        return $str;
    $word = 0;
    $i = 0;
    /** 字符串指针 **/
    $stag = array(array());
    /** 存放开始HTML的标志 **/
    $etag = array(array());
    /** 存放结束HTML的标志 **/
    $sp = 0;
    $ep = 0;
    while ($word != $num) {
        if (ord($str[$i]) > 128) {
            //$re.=substr($str,$i,3);
            $i += 3;
            $word++;
        } else if ($str[$i] == '<') {
            if ($str[$i + 1] == '!') {
                $i++;
                continue;
            }

            if ($str[$i + 1] == '/') {
                $ptag =& $etag;
                $k =& $ep;
                $i += 2;
            } else {
                $ptag =& $stag;
                $i += 1;
                $k =& $sp;
            }
            for (; $i < $leng; $i++) {
                if ($str[$i] == ' ') {
                    $ptag[$k] = implode('', $ptag[$k]);
                    $k++;
                    break;
                }
                if ($str[$i] != '>') {
                    $ptag[$k][] = $str[$i];
                    continue;
                } else {
                    $ptag[$k] = implode('', $ptag[$k]);
                    $k++;
                    break;
                }
            }
            $i++;
            continue;
        } else {
            //$re.=substr($str,$i,1);
            $word++;
            $i++;
        }
    }
    foreach ($etag as $val) {
        $key1 = array_search($val, $stag);
        if ($key1 !== false) unset($stag[$key1]);
    }
    foreach ($stag as $key => $val) {
        if (in_array($val, array('br', 'img'))) unset($stag[$key1]);
    }
    array_reverse($stag);
    $ends = '</' . implode('></', $stag) . '>';
    $re = substr($str, 0, $i) . $ends;
    if ($more) $re .= '...';
    return $re;
}

//远程图片本地化
function local_remotepic($htmlData){
    preg_match_all("/<.+?>/s", strip_tags($htmlData, "<img>"), $r); //获取全部 img 标记作为替换本地文件时的依据
    $t = array();
    foreach($r[0] as $i=>$f) {
        preg_match("/src=(\"|')?([^\"]+)('|\")?/i", $f, $u); //获取远程图片的url
        $remote_img = $u[2];
        if(strtolower(substr($remote_img,0,4)) == 'http'){
            if(strlen($remote_img)>4) {
                $end_pre = substr($remote_img,-4);
                $s = get_url_contents($remote_img); //读取远程图片
                $fn = "/Public/Editor/attached/image/".time().mt_rand(111111,999999).$end_pre;
                file_put_contents('.'.$fn, $s); //写入本地
                // $fn = 'http://www.dami.com'.$fn;//这里可以加您自己域名http://www.dami.com
                $htmlData = str_ireplace($remote_img, $fn, $htmlData);
            }
        }
    }
    return $htmlData;
}

//对数组递归函数处理
function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
{
    static $recursive_counter = 0;
    if (++$recursive_counter > 2000) {
        die('possible deep recursion attack');
    }
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            arrayRecursive($array[$key], $function, $apply_to_keys_also);
        } else {
            $array[$key] = $function($value);
        }
        if ($apply_to_keys_also && is_string($key)) {
            $new_key = $function($key);
            if ($new_key != $key) {
                $array[$new_key] = $array[$key];
                unset($array[$key]);
            }
        }
    }
    $recursive_counter--;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) unset($arr[$pos]);
        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code)
{
    static $_status = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );
    if (isset($_status[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:' . $code . ' ' . $_status[$code]);
    }
}

/**
 * 缓存管理
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
function S($name, $value = '', $options = 3600)
{
    //$options=0 永久 也可以具体时间2020-02-05 15:00:00
    return cache($name, $value, $options);
}

function M($name,$save = false,$with_prefix=true){
    if($save){
        $model = new \app\base\model\General([],$name);
        return $model;
    }
    return $with_prefix?\think\facade\Db::name($name):\think\facade\Db::table($name);
}
function D($name){
    try {
        if(strpos($name,'\\')===false){
            $name = '\\app\\base\\model\\'.ucfirst($name);
            if(!class_exists($name)){
                $name = app()->getNamespace().'\\model\\'.ucfirst($name);
            }
        }
        return new $name();
    }catch (\Exception $e){
        throw new ClassNotFoundException('class not exists: ' . $name, $name);
        return false;
    }
}

function U($url,$par = []){
    return (string)url($url,$par);
}