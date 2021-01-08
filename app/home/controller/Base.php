<?php
declare (strict_types = 1);
namespace app\home\controller;

use app\base\model\ArticleView;
use app\home\middleware\After;
use app\home\middleware\Before;
use think\App;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\Config;
use think\facade\Request;
use think\Response;
use think\Validate;

use app\common\model\System;
/**
 * 控制器基础类
 */
abstract class Base
{
    protected $appName;        //当前应用名称
    protected $controllerName; //获取当前的控制器名
    protected $public;         //公共目录
    protected $template;       //模板目录
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;


    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [Before::class];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        // 查找所有系统设置表数据
        $this->template = './template/'.cookie('think_templatge').'/';
        config('view.view_path',$this->template);
        //获取访客权限
        if (intval(cookie('first_look')) != 1 && !session('dami_uid')) {
            if (config('basic.isread') == 1) {
                $group_vail = get_field('member_group','group_id=' . config('basic.defaultup'),'group_vail');
                seesion('dami_uservail', $group_vail);
            }
            cookie('first_look', 1);
        }
    }

    //是否使用静态
    protected function html_init($static_file, $php_file, $is_build)
    {
        clearstatcache();
        if (file_exists($static_file) && $is_build == 1 && session('is_rebuild') != 1 && (time() - filemtime($static_file)) <= 21600 && filemtime($php_file) - filemtime($static_file) <= 0) //判断源文件已修改//缓存6小时 永久的化去掉他
        {
            //View::fetch($static_file); 用这句容易产生注入漏洞因为静态页里的PHP代码仍然解析
            $fp = fopen($static_file, "r");
            $conStr = fread($fp, filesize($static_file));
            fclose($fp);
            echo $conStr;
            exit();
        }
    }

    protected function lists($typeid, $mode, $limit, $param, $order = 'addtime desc')
    {
        //查询数据库
        $article = new ArticleView();
        //封装条件
        $map[] = ['status','=',1];
        //准备工作
        //追影 QQ:279197963修改让其支持无限极
        $arr = get_children($typeid);
        //模式判断
        switch ($mode) {
            case 0:
                $map[] = ['article.typeid','in', $arr];
                break;
            case 1:
                $map[] = ['article.typeid','=',$typeid];
                break;
            default:
                $map[] = ['article.typeid','in', $arr];
                break;
        }
        $alist = $article->getTableInstance()->where($map)->orderRaw($order)->limit($limit)->select();
        $this->assign($param, $alist);
    }

    //获取子孙目录
    protected function children_dir($typeid = 22, $assign = 'product_dir', $show_all = 0)
    {
        $dao = M('type');
        $t = $dao->where('typeid =' . $typeid)->find();
        if ($t) {
            if ($show_all == 1) {
                $str = $t["path"] . "-" . $t["typeid"];
                $mylist = $dao->where("1 = instr(path,'" . $str . "')")->select();
            } else {
                $mylist = $dao->where("fid = " . $t["typeid"])->select();
            }
            $this->assign($assign, $mylist);
        }
    }

//获取家族树
    protected function tree_dir($typeid, $assign = 'tree_list')
    {
        if ($typeid == 0) {
            exit();
        }
        $str = get_all_tree($typeid);
        $dao = M('type');
        $t = $dao->where('typeid in(' . $str . ')')->order('typeid')->select();
        $this->assign($assign, $t);
    }

    
    /**
     * 操作错误跳转
     * @param  mixed   $msg 提示信息
     * @param  string  $url 跳转的URL地址
     * @param  mixed   $data 返回的数据
     * @param  integer $wait 跳转等待时间
     * @return void
     */
    protected function error($msg = '', string $url = null, $data = '', int $wait = 3): Response
    {
        if (is_null($url)) {
            $url = request()->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : app('route')->buildUrl($url)->__toString();
        }

        $result = [
            'code' => 0,
            'message'  => $msg,
            'data' => $data,
            'jumpUrl'  => $url,
            'waitSecond' => $wait,
        ];
        $err = (string)app('config')->get('app.error_tmpl');
        $type = (request()->isJson() || request()->isAjax()) ? 'json' : 'html';
        if ($type == 'html'){
            $response = view($err, $result);
        } else if ($type == 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }

    /**
     * 返回封装后的API数据到客户端
     * @param  mixed   $data 要返回的数据
     * @param  integer $code 返回的code
     * @param  mixed   $msg 提示信息
     * @param  string  $type 返回数据格式
     * @param  array   $header 发送的Header信息
     * @return Response
     */
    protected function ajaxReturn($data, $info='', $status=1, $type='', array $header = [], int $code=0): Response
    {
        $result = [
            'code' => $code,
            'status'  => $status,
            'info' => $info,
            'data' => $data,
        ];

        $type     = $type ?: 'json';
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * 操作成功跳转
     * @param  mixed     $msg 提示信息
     * @param  string    $url 跳转的URL地址
     * @param  mixed     $data 返回的数据
     * @param  integer   $wait 跳转等待时间
     * @return void
     */
    protected function success($msg = '', string $url = null, $data = '', int $wait = 3): Response
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : app('route')->buildUrl($url, get_back_url())->__toString();
        }

        $result = [
            'code' => 1,
            'message'  => $msg,
            'data' => $data,
            'jumpUrl'  => $url,
            'waitSecond' => $wait,
        ];

        $type = (request()->isJson() || request()->isAjax()) ? 'json' : 'html';
        if ($type == 'html'){
            $response = view(Config::get('app.success_tmpl'), $result);
        } else if ($type == 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }
}