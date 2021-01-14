<?php
declare (strict_types=1);

namespace app;

use think\App;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\Config;
use think\facade\View;
use think\Response;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
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
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

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
    }

    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
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
            $v = new $class();
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

    /**
     * 返回封装后的API数据到客户端
     * @param mixed $data 要返回的数据
     * @param integer $code 返回的code
     * @param mixed $msg 提示信息
     * @param string $type 返回数据格式
     * @param array $header 发送的Header信息
     * @return Response
     */
    protected function ajaxReturn($data, $info = null, $status = null, $type = '', array $header = [], int $code = 0): Response
    {
        $result = ($type=='html' || $type=='xml' || (is_array($data) && !$info && !$status))?$data:[
            'code' => $code,
            'status' => $status,
            'info' => $info,
            'data' => $data,
        ];
        $type = $type ?: 'json';
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * 操作成功跳转
     * @param mixed $msg 提示信息
     * @param string $url 跳转的URL地址
     * @param mixed $data 返回的数据
     * @param integer $wait 跳转等待时间
     * @return void
     */
    protected function success($msg = '', string $url = null, $data = '', int $wait = 3): Response
    {
        $result = [
            'code' => 1,
            'message' => $msg,
            'data' => $data,
            'waitSecond' => $wait,
        ];
        if($url){
            $result['jumpUrl'] = $url;
        }
        $type = (request()->isJson() || request()->isAjax()) ? 'json' : 'html';
        if ($type == 'html') {
            $response = view(Config::get('app.success_tmpl'), $result);
        } else if ($type == 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }


    /**
     * 操作错误跳转
     * @param mixed $msg 提示信息
     * @param string $url 跳转的URL地址
     * @param mixed $data 返回的数据
     * @param integer $wait 跳转等待时间
     * @return void
     */
    protected function error($msg = '', string $url = null, $data = '', int $wait = 4): Response
    {
        $result = [
            'code' => 0,
            'error' => $msg,
            'data' => $data,
            'waitSecond' => $wait,
        ];
        if($url){
            $result['jumpUrl'] = $url;
        }
        $err = (string)app('config')->get('app.error_tmpl');
        $type = (request()->isJson() || request()->isAjax()) ? 'json' : 'html';
        if ($type == 'html') {
            $response = view($err, $result);
        } else if ($type == 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }
//兼容以前版本的写法
    public function assign($var,$value){
        View::assign($var,$value);
    }

    public function display($tpl='',$data = []){
        return View::fetch($tpl,$data);
    }

    public function fetch($tpl=null,$data =[]){
        return View::display($tpl,$data);
    }

    public function redirect($url){
        $url = url($url);
        return redirect((string)$url);
    }

    public function verify_token(){
        $check = $this->request->checkToken(config('app.TOKEN_NAME'));
        if(false === $check) {
            $this->error('Token验证失败');
        }
    }

}
