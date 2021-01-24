<?php
namespace payment;
class Alipay extends Common implements Payinterface{
    protected $code = 'alipay';
    public function gateway($data){
        $url = "http://" . $_SERVER['HTTP_HOST'] .  "/trade/ap_jishi/alipayapi.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        echo $output;exit();
    }
    public function donotify(){
        $alipay_config = [];
        require_once("./trade/ap_jishi/alipay.config.php");
        require_once("./trade/ap_jishi/lib/alipay_notify.class.php");
//计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {//验证成功
            //商户订单号WIDout_trade_no
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];

            $total_fee = $_POST['total_fee'];
            if ($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                M('member_trade')->whereRaw("out_trade_no='{$out_trade_no}' or group_trade_no='{$out_trade_no}'")->save(['status'=>0]);
                logResult('等待买家付款!');
                echo "success";        //请不要修改或删除
            } else if ($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                M('member_trade')->whereRaw("out_trade_no='{$out_trade_no}' or group_trade_no='{$out_trade_no}'")->save(['status'=>1]);
                logResult('已付款，等待发货!');
                echo "success";        //请不要修改或删除


                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
                //该判断表示卖家已经发了货，但买家还没有做确认收货的操作

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                M('member_trade')->whereRaw("out_trade_no='{$out_trade_no}' or group_trade_no='{$out_trade_no}'")->save(['status'=>2]);
                logResult('已发货,等待收货!');
                echo "success";        //请不要修改或删除

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $this->payed_worker($trade_no,$out_trade_no,$total_fee);
                echo "success";        //请不要修改或删除
            } //退款退货相关
            else if ($_POST['refund_status'] == 'WAIT_SELLER_AGREE') {
                M('member_trade')->whereRaw("out_trade_no='{$out_trade_no}' or group_trade_no='{$out_trade_no}'")->save(['status'=>7]);
            } else if ($_POST['refund_status'] == 'WAIT_BUYER_RETURN_GOODS') {
                M('member_trade')->whereRaw("out_trade_no='{$out_trade_no}' or group_trade_no='{$out_trade_no}'")->save(['status'=>8]);
            } else if ($_POST['refund_status'] == 'REFUND_SUCCESS') {
                M('member_trade')->whereRaw("out_trade_no='{$out_trade_no}' or group_trade_no='{$out_trade_no}'")->save(['status'=>9]);

            } else if ($_POST['trade_status'] == 'TRADE_CLOSED') {
                M('member_trade')->whereRaw("out_trade_no='{$out_trade_no}' or group_trade_no='{$out_trade_no}'")->save(['status'=>10]);
            } else {
                logResult($out_trade_no . '<BR>');
            }
        } else {
            //验证失败
            logResult('验证失败<BR>');
            echo "fail";
        }
    }

    public function doreturn(){
        exit(get_url_contents('/trade/ap_jishi/return_url.php'));
    }

    public function refund($data){
        exit(get_url_contents('/trade/ap_jishi/refund.php',$data));
    }

}