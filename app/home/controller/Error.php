<?php
namespace app\home\controller;

class Error extends Base
{
    public function __call($method, $args)
    {
        header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码
        return $this->display(config('app.404_tmpl'));
    }
}