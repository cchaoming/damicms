<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace app\home\middleware;

use Closure;
use think\App;
use think\Request;
use think\Response;

class ControllerBefore
{
    /**
     *
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        //静态化检测
        $is_build = config('basic.is_build_html');
        if($is_build){
            //允许参数
            ob_start();
            $request_data = $request->param();
            $allow_param = ['page', 'author_id','keyword','p','typceid'];
            $static_file = './html/' . TMPL_NAME . '/' . MODULE_NAME.'/'.ACTION_NAME;
            $mid_str = '';
            if (is_array($request_data)) {
                foreach (is_array($request_data) as $k => $v) {
                    if (in_array($k, $allow_param)) {
                        $mid_str .= '/' . strval($k) . '/' . md5($v);
                    }
                }
            }
            $static_file .= ($mid_str . '.html');
            define('HTML_STATIC_FILE', $static_file);
            $php_file = app()->getAppPath().'controller'.DIRECTORY_SEPARATOR.MODULE_NAME.'.php';
            $this->html_init($static_file, $php_file, $is_build);
        }
        return $next($request);
    }

    //是否使用静态
    private function html_init($static_file, $php_file, $is_build)
    {
        clearstatcache();
        if (file_exists($static_file) && $is_build == 1 && session('is_rebuild') != 1 && (time() - filemtime($static_file)) <= 21600 && filemtime($php_file) - filemtime($static_file) <= 0) //判断源文件已修改//缓存6小时 永久的化去掉他
        {
            $fp = fopen($static_file, "r");
            $conStr = fread($fp, filesize($static_file));
            fclose($fp);
            echo $conStr;
            exit();
        }
    }

    public function end(Response $response)
    {
        $is_build = config('basic.is_build_html');
        if($is_build && defined('HTML_STATIC_FILE')){
            //允许参数
            $static_file = HTML_STATIC_FILE;
            $c = $response->getContent();
            if (!file_exists(dirname($static_file))) {
                @mk_dir(dirname($static_file));
            }
            @file_put_contents($static_file, $c);
        }
    }
}
