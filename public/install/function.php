<?php
//清空目录
function clean_dir($path)        {
        if (!is_dir($path))        {
                if (is_file($path))        {
                        unlink($path);
                }
                return;
        }
        $p=opendir($path);
        while ($f=readdir($p))        {
                if ($f=="." || $f=="..") continue;
                clean_dir($path.$f);
        }
        @rmdir($path);
        return;
}
//gd版本信息
function gdversion()
    {
        //没启用php.ini函数的情况下如果有GD默认视作2.0以上版本
        if(!function_exists('phpinfo'))
        {
            if(function_exists('imagecreate'))
            {
                return '2.0';
            }
            else
            {
                return 0;
            }
        }
        else
        {
            ob_start();
            phpinfo(8);
            $module_info = ob_get_contents();
            ob_end_clean();
            if(preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info,$matches))
            {
                $gdversion_h = $matches[1];
            }
            else
            {
                $gdversion_h = 0;
            }
            return $gdversion_h;
        }
    }
//可写测试
function TestWrite($d)
{
    if(is_file($d)){
        if (is_writable ($d)) {
            return true;
        } else {
            return false;
        }
    }
    $tfile = '_dedet.txt';
    $d = preg_replace("#\/$#", '', $d);
    $fp = @fopen($d.'/'.$tfile,'w');
    if(!$fp) return false;
    else
    {
        fclose($fp);
        $rs = @unlink($d.'/'.$tfile);
        if($rs) return true;
        else return false;
    }
}

function is_php( $version = '7.1.0' ) {
    $php_version = explode( '-', phpversion() );
    // =0表示版本为5.0.0  ＝1表示大于5.0.0  =-1表示小于5.0.0
    $is_pass = strnatcasecmp( $php_version[0], $version ) >= 0 ? true : false;
    return $is_pass;
}
?>