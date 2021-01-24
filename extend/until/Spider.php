<?php
/**
 * 采集程序类
 * @author  追影 QQ:279197963
 * @version 1.0.0
 */

namespace until;
use think\Exception;
use think\facade\Db;

include_once "phpQuery.php";
class Spider
{
// 采集的终端页地址
    private $pages = array();
    /**
     * 采集结果
     *
     * @private array
     */
    private $result = array();
    /**
     * 第一层链接页面
     *
     * @private array
     */
    private $startUrls = array();
    /**
     * 超时时间
     *
     * @private integer
     */
    private $timeout;
    /**
     * 正在处理的文件内容
     *
     * @private string
     */
    private $httpContent;
    /**
     * 正在处理的文件头
     *
     * @private array
     */
    private $httpHead = array();
    /**
     * 自定义的head数组
     *
     * @private array
     */
    private $putHead = array();
    /**
     * 采集字段与规则数组
     *
     * @private array
     */
    private $field_arr = array();
    /**
     * 采集层次数
     *
     * @private interger
     */
    private $deep;
    /**
     * 采集层次结构
     *
     * @private array
     */
    private $layout_arr = array();
    /**
     * 采集限制条数
     *
     * @private integer
     */
    private $limit = 0;
    /**
     * 程序运行时间
     *
     * @private float
     */
    private $runtime = 0;
    /**
     * 被采集页面编码
     *
     * @private string
     */
    private $charset = 'UTF-8';
    public $islocal = 0;
    /**
     * 页面引用地址
     *
     * @private string
     */
    private $httpreferer;
    private $pagelimit = 0;
    private $filepath = './';

    function spider()
    {
        $this->timeout = 300;
    }

    /**
     * 运行采集
     *
     * @return array
     */
    function run()
    {
		echo '<link href="/Public/Admin/images/Admin_css.css" type="text/css" rel="stylesheet">';
        $begintime = $this->microtime_float();
        $cnt = 1;
        foreach ($this->startUrls as $starturl) {
            /**
             * 解析出起始地址中的页码区间
             */
            $nowurl = $starturl;
            //打开缓冲区
            if (ob_get_level() == 0)
            {
                ob_start();
            }

            if (preg_match("~\{(\d+),(\d+)\}~", $starturl, $pagenum)) {
                $pagebegin = intval($pagenum[1]);
                $pageend = intval($pagenum[2]);
//多页
                for ($i = $pagebegin; $i <= $pageend; $i++) {
                    $starturl = str_replace($pagenum[0], $i, $nowurl);
                    echo '当前列表页:' . $starturl . '<br>';
                    $urllists = $this->getLists($this->layout_arr[0]['pattern'], $this->getContent($starturl));
                    foreach ($urllists as $url) {
                        echo '正在采集:' . $url . '<br>';
                        ob_flush();
                        flush();
                        if (($this->limit > 0 && $cnt <= $this->limit) || $this->limit == 0) {
                            $this->filterContent($this->getContent($url, $starturl));
                            $cnt++;
                        }
                    }
                }
                ob_end_flush();//清空浏览器缓存

            } else {
                echo '当前列表页:' . $starturl . '<br>';
                $urllists = $this->getLists($this->layout_arr[0]['pattern'], $this->getContent($starturl));
                foreach ($urllists as $url) {
                    echo '正在采集:' . $url . '<br>';
                    ob_flush();
                    flush();
                    if (($this->limit > 0 && $cnt <= $this->limit) || $this->limit == 0) {
                        $this->filterContent($this->getContent($url, $starturl));
                        $cnt++;
                    }
                }
                ob_end_flush();//清空浏览器缓存
            }
        }
        $this->runtime = $this->microtime_float() - $begintime;
        return $this->result;
    }

    /**
     * 从文字段中根据规则提取出url列表
     *
     * @param string $pattern
     * @param string $content
     * @return Array
     */
    function getLists($pattern = '', $content = '')
    {
        if (strpos($pattern, '{*}') === false && strpos($pattern, '{(\d)+}') === false) return array($pattern);
        $pattern = preg_quote($pattern);
        $pattern = str_replace('\{', "", $pattern);
        $pattern = str_replace('\}', "", $pattern);
        $pattern = str_replace('\*', '([^\'\">]*)', $pattern);
        $pattern = str_replace('\(\\\\d\)\+', '((\d)+)', $pattern);
//echo $pattern;
        $pattern = "~" . $pattern . "~is";
        preg_match_all($pattern, $content, $preg_rs);
        return array_unique($preg_rs[0]);
    }

    /**
     * 获取指定url的html内容包括头
     *
     * @param string $url
     * @return string
     */
    function getContent($url, $referer = '')
    {
        $url = $this->urlRtoA($url, $referer);
        $content = get_url_contents($url);
        $this->getHead($content);
        $this->httpreferer = $referer;
        return $content;
    }

    /**
     * 按照规则从内容提取所有字段
     * @param Array
     * @return Array
     */
    function filterContent($content = '')
    {
        $rs = array();
        foreach ($this->field_arr as $field => $fieldinfo) {
            $rs[$field] = $this->getPregField($fieldinfo, $content, $this->httpreferer);
        }
        $this->result[] = $rs;
    }

    /**
     * 本地化文章中的图片路径
     * @param string $xstr 内容
     * @param string $keyword 创建照片的文件名
     * @param string $oriweb 网址
     * @return string
     *
     */
    function replaceimg($xstr, $url)
    {
        preg_match_all('/<img.*?src=[\"|\']?(.+\.(jpg|gif|bmp|bnp|png))[\"|\']?.+>/i', $xstr, $match);
        foreach ($match[1] as $imgurl) {
            if (is_int(strpos($imgurl, 'http'))) {
                //url完整路径
                $arcurl = $imgurl;
            } else if (substr($imgurl, 0, 1) == '/') {
                //绝对路径
                $arr_url = parse_url($url);
                $arcurl = $arr_url['scheme'] . '://' . $arr_url['host'] . $imgurl;
            } else {
                //相对路径
                $arcurl = dirname($url) . '/' . $imgurl;
            }
            $saveimgfile = $arcurl;
            //保存到本地
            if ($this->islocal == 1) {
                $d = date('Ymd', time());
                $dirslsitss = './Public/image/' . $d;//分类是否存在
                if (!is_dir($dirslsitss)) {
                    @mkdir($dirslsitss, 0777);
                }
                $path_parts = pathinfo($arcurl);
                $img = file_get_contents($arcurl);
                if (!empty($img)) {
                    $fileimgname = time() . "-" . rand(1000, 9999) . '.' . $path_parts['extension'];
                    $filecachs = $dirslsitss . "/" . $fileimgname;
                    $fanhuistr = file_put_contents($filecachs, $img);
                    $saveimgfile = "/Public/image/" . $d . "/" . $fileimgname;
                    //echo  $saveimgfile;
                }
            }
            //echo $saveimgfile;
            //echo $imgurl;
            $xstr = str_replace($imgurl, $saveimgfile, $xstr);
            //var_dump($xstr);
        }
        return $xstr;
    }

    /**
     * 相对路径转化为绝对路径
     *
     * @param string $relative
     * @param string $referer
     * @return string
     */
    function urlRtoA($relative, $referer)
    {
        /**
         * 去除#后面的部分
         */
        $pos = strpos($relative, '#');
        if ($pos > 0) $relative = substr($relative, 0, $pos);
        /**
         * 检测路径如果是绝对地址直接返回
         */
        if (preg_match("~^(http|ftp)://~i", $relative))
            return $relative;
        /**
         * 解析引用地址，获得协议,主机等信息
         */
        preg_match("~((http|ftp)://([^/]*)(.*/))([^/#]*)~i", $referer, $preg_rs);
        $parentdir = $preg_rs[1];
        $petrol = $preg_rs[2] . '://';
        $host = $preg_rs[3];
        /**
         * 如果以/开头的情况
         */
        if (preg_match("~^/~i", $relative)) {
            return $petrol . $host . $relative;
        }
        return $parentdir . $relative;
    }

    /**
     * 根据规则提取一个字段
     *
     * @param string $pattern
     * @param string $content
     * @return string
     */
    function getPregField($fieldinfo, $content, $url = '')
    {
        /**
         * 不再用正则节点查找太不准确 注意:去掉不需要匹配的
         */
        if ($fieldinfo['field'] == 'typeid' || $fieldinfo['field'] == 'addtime') {
            return $fieldinfo['pattern'];
        }
        if (stripos($fieldinfo['pattern'], 'eq(') === false){
            $findnode = $fieldinfo['pattern'] . ':eq(0)';
        }
        //echo $findnode;
        $doc = \phpQuery::newDocument($content);
        $fieldresult = pq($findnode, $doc)->html();
        //echo '结果:'.$fieldresult;
        //unset(phpQuery::$documents);
        unset($doc);
        /**
         * 去掉换行符
         */
        $fieldresult = preg_replace("~[\r\n]*~is", '', $fieldresult);
//图片本地化处理
        if ($fieldinfo['field'] == 'content') {
            $fieldresult = $this->replaceimg($fieldresult, $url);
        }
        /**
         * 对采集到的结果根据规则再进行二次替换处理
         */
        $replace_arr = $fieldinfo['replace'];
        if (is_array($replace_arr)) {
            $replace_arr[0] = "~" . $replace_arr[0] . "~s";
            $fieldresult = preg_replace($replace_arr[0], $replace_arr[1], $fieldresult);
        }
        /**
         * 针对有下一页的字段递归采集
         */
        if ($this->pagelimit == 0) {
            if ($fieldinfo['nextpage'] != '') {
                $pattern = $fieldinfo['nextpage'];
                $pattern = str_replace('{nextpage}', '(?P<nextpage>[^\'\">]*?)', $pattern);
                $pattern = "~" . $pattern . "~is";
                if (preg_match($pattern, $content, $preg_rs) && $preg_rs['nextpage'] != '') {
                    $fieldresult .= $this->getPregField($fieldinfo, $this->getContent($preg_rs['nextpage'], $this->httpreferer));
                }
            }
        }
        if (!empty($fieldinfo['callback'])) $fieldresult = $fieldinfo['callback']($fieldresult);
        return $fieldresult;
    }

    /**
     * 添加一个采集字段和规则
     *
     * @param string $field
     * @param string $pattern
     */
    function addField($field, $pattern, $replace_arr = '', $isregular = 'false', $nextpage = '', $callback = '')
    {
        $rs = array(
            'field' => $field,
            'pattern' => $pattern,
            'replace' => $replace_arr,
            'isregular' => $isregular,
            'nextpage' => $nextpage,
            'callback' => $callback
        );
        $this->field_arr[$field] = $rs;
    }

    /**
     * 输出
     *
     */
    function output()
    {
        echo "采集完成！历时:";
        echo "$this->runtime ";
    }

    /**
     * 输出到XLS文件
     *
     * @param string $file
     */
    function saveXls($file = 'spider_result.xls')
    {
        $fp = fopen($file, 'w');
        if ($fp) {
            foreach ($this->result as $result) {
                $line = implode("\t", $result) . "\n";
                fputs($fp, $line);
            }
        }
        fclose($fp);
//echo 'The result has been saved to '.$file.'.<br/>Cost time:'.$this->runtime;
    }

    function saveSql($table = 'dami_article', $file = 'dami_caiji.sql', $act = 0)
    {
        $sql_key = '';
        $fp = fopen($file, 'w');
        if ($fp) {
            foreach ($this->field_arr as $fieldinfo) {
                $sql_key .= ', `' . $fieldinfo['field'] . '`';
            }
            $sql_key = substr($sql_key, 1);
            foreach ($this->result as $result) {
                $sql_value = array();
                foreach ($result as $key => $value) {
                    $sql_value[] = "'" . $this->addslash($value) . "'";
                }
                $line = "REPLACE INTO `$table` ( $sql_key ) VALUES (" . join(', ', $sql_value) . ");";
                if ($act == 0) {
                    $line .= "\r\n";
                }
                if ($act == 1) {
					$encoding = mb_detect_encoding($line, array('UTF-8','GB2312','GBK','UTF-16','UCS-2','BIG5','ASCII'));
					if ($encoding != false) {
					  $line = iconv($encoding, 'UTF-8', $line);
					}else{
					  $line = mb_convert_encoding($line, 'UTF-8','Unicode');
					}
					//echo $line;
                    Db::execute($line);
//mysql_query($line);
                }
                fputs($fp, $line);
            }
        }
        fclose($fp);
        if(substr($file,0,1)=='.'){$file= substr($file,1);}
        echo 'Sql文件保存在:<a href="'.$file.'">' . $file . '</a><br/><input name="button2" type="button" class="btn btn-success" value="返回" onClick="history.go(-1);">';
    }

    /**
     * 取得响应内容的头部信息
     *
     * @param string $content
     * @return array
     */
    function getHead($content)
    {
        $head = explode("\r\n\r\n", $content);
        $head = $head[0];
//  echo $head;
        if (!preg_match("~charset\=(.*)\r\n~i", $head, $preg_rs))
            preg_match('~charset=([^\"\']*)~i', $content, $preg_rs);
        $this->httpHead['charset'] = strtoupper(trim($preg_rs[1]));
//  preg_match("~charset\=(.*)~i",$head,$preg_rs);
        return $this->httpHead;
    }

    /**
     * 设置采集页面的编码
     * 在程序不能自动识别的情况下采集前要手动调用此函数
     *
     * @param string $charset
     */
    function setCharset($charset)
    {
        $this->charset = strtoupper($charset);
    }

    /**
     * 设置第一层链接页面地址
     *
     * @param array $url_arr
     */
    function setStartUrls($url_arr)
    {
        $this->startUrls = $url_arr;
    }

    /**
     * 增加一个第一层链接页面地址
     *
     * @param string $url
     */
    function addStartUrl($url)
    {
        $this->startUrls[] = $url;
    }

    /**
     * 添加一个采集层次
     *
     * @param integer $deep
     * @param string $layout
     * @param boolean $isSimple
     * @param boolean $isPageBreak
     * @param string $pattern
     */
    function addLayer($deep, $layout, $pattern = '', $isSimple = 'false', $isPageBreak = 'false')
    {
        $this->layout_arr[$deep] = array(
            'layout' => $layout,
            'isSimple' => $isSimple,
            'isPageBreak' => $isPageBreak,
            'pattern' => $pattern);
    }

    /**
     * 自定义head
     * @param string $namespace
     * @param string $value
     */
    function setHead($name, $value)
    {
        $this->putHead[$name] = $value;
    }

    /**
     * 清除html代码
     * @param string $content ;
     * @param string $cleartags
     * @return string
     */
    function clearHtml($content, $cleartags = 'div')
    {
        $cleartags_arr = explode('|', $cleartags);
        foreach ($cleartags_arr as $cleartag) {
            $pattern = '~<\/?' . $cleartag . '[^>]*>~is';
            $content = preg_replace($pattern, '', $content);
        }
        return $content;
    }

    /**
     * 日志
     *
     */
    function log($str)
    {
        echo $str . "<br/>\n";
    }

    /**
     * 获取采集运行时间
     *
     * @return float
     */
    function getRuntime()
    {
        return $this->runtime;
    }

    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    function addslash($string)
    {
        return addslashes($string);
    }
}

?>