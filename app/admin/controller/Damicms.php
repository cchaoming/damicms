<?php
namespace app\admin\controller;
use app\BaseController;
use think\exception\HttpResponseException;
use think\facade\View;
use think\Response;

class Damicms extends BaseController {

    private function dispatchJump($message,$status=1,$jumpUrl='',$ajax=false)
    {
        // 判断是否为AJAX返回
        if($ajax) $this->ajaxReturn($ajax,$message,$status);
        // 提示标题
        $this->assign('msgTitle',$status? 'success' : 'fail');
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if(View::__isset('closeWin')){$this->assign('jumpUrl','javascript:window.close();');}
        $this->assign('status',$status);   // 状态
        $this->assign('message',$message);// 提示信息
        if($status) { //发送成功信息
            // 成功操作后默认停留1秒
            if(!View::__isset('waitSecond')){$this->assign('waitSecond',"1");}
            // 默认操作成功自动返回操作前页面
            if(!View::__isset('jumpUrl')){$this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);}
            $result = $this->display('File/null');
        }else{
            //发生错误时候默认停留3秒
            if(!View::__isset('waitSecond')) {$this->assign('waitSecond',"3");}
            // 默认发生错误的话自动返回上页
            if(!View::__isset('jumpUrl')){$this->assign('jumpUrl',"javascript:history.back(-1);");}
            $result = $this->display('File/null');
        }
        $response = Response::create($result);
        throw new HttpResponseException($response);
        // 中止执行  避免出错后继续执行
        exit ;
    }

    protected function success($msg = '', string $url = null, $data = '', int $wait = 3): Response
    {
        $this->dispatchJump($msg,1,$url);
    }

    protected function error($msg = '', string $url = null, $data = '', int $wait = 3): Response
    {
       $this->dispatchJump($msg,0,$url);
    }
}