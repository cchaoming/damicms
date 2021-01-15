<?php
/***********************************************************
    [大米CMS] (C)2011 - 2011 damicms.com
    
	@function 大米CMS基类--改写Action

    @Filename DamicmsAction.class.php $

    @Author 追影 QQ:279197963 $

    @Date 2011-11-14 08:45:20 $
*************************************************************/
//改写Action for FileAction 
class DamicmsAction extends Action{
	 /**
     +----------------------------------------------------------
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     +----------------------------------------------------------
     * @param string $message 提示信息
     * @param Boolean $status 状态
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	 private function dispatchJump($message,$status=1,$jumpUrl='',$ajax=false)
    {
        // 判断是否为AJAX返回
        if($ajax || $this->isAjax()) $this->ajaxReturn($ajax,$message,$status);
        // 提示标题
        $this->assign('msgTitle',$status? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if($this->get('closeWin'))    $this->assign('jumpUrl','javascript:window.close();');
        $this->assign('status',$status);   // 状态
        $this->assign('message',$message);// 提示信息
        //保证输出不受静态缓存影响
        C('HTML_CACHE_ON',false);
        if($status) { //发送成功信息
            // 成功操作后默认停留1秒
            if(!$this->get('waitSecond'))    $this->assign('waitSecond',"1");
            // 默认操作成功自动返回操作前页面
            if(!$this->get('jumpUrl')) $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
            $this->display('null');
        }else{
            //发生错误时候默认停留3秒
            if(!$this->get('waitSecond'))    $this->assign('waitSecond',"3");
            // 默认发生错误的话自动返回上页
            if(!$this->get('jumpUrl')) $this->assign('jumpUrl',"javascript:history.back(-1);");
            $this->display('null');
        }
        if(C('LOG_RECORD')) Log::save();
        // 中止执行  避免出错后继续执行
        exit ;
    }
	
	 protected function success($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,1,$jumpUrl,$ajax);
    }
	
	 protected function error($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,0,$jumpUrl,$ajax);
    }
   
    }
?>