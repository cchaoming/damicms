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

namespace app\base\middleware;

use Closure;
use think\App;
use think\Request;

/**
 * 静态变量
 */
class StaticVar
{

    /** @var App */
    protected $app;

    protected $request;

    public function __construct(App $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * Session初始化
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        define('APP_NAME', app('http')->getName());
        define('MODULE_NAME', $request->controller());
        define('ACTION_NAME', $request->action());
        define('TMPL_PATH', './template/');
        $cookie_template = cookie('think_template');
        if (!$cookie_template) {
            $default_theme = check_wap()?config('app.DEFAULT_WAP_THEME'):config('app.DEFAULT_THEME');
            define('TMPL_NAME', $default_theme);
            cookie('think_template', $request->param('t', $default_theme));
        }else if($request->param('t')){
            //绝对路径app()->getRootPath().'public'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.strval($request->param('t'))
            if(is_dir(TMPL_PATH.strval($request->param('t')))){
                $cur_theme = strval($request->param('t'));
                define('TMPL_NAME', $cur_theme);
                cookie('think_template', $cur_theme);
            }
        }else{
            define('TMPL_NAME', $cookie_template);
        }
        //防止注入(后台post可能含有关键字不过滤)
        $request_data = $request->param();
        if(is_array($request_data) && APP_NAME != 'admin'){
        require_once('./php_safe.php');
        array_walk_recursive($request_data, 'inject_check');
        array_walk_recursive($request_data, 'remove_xss');
        }
        return $next($request);
    }
}
