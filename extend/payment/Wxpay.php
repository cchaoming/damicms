<?php
namespace payment;
class Alipay extends Common implements Payinterface{
    protected $code = 'wxpay';
    public function gateway($data){
        $is_wx = is_weixin_visit();
        $url = "http://" . $_SERVER['HTTP_HOST'] . !$is_wx?"/trade/Wxpay/dopay/jsapi.php":"/trade/Wxpay/dopay/native.php";
        $output = get_url_contents($url,$data);
        echo $output;exit();
    }
    public function donotify(){
        require_once "./trade/Wxpay/lib/WxPay.Api.php";
        //$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml = file_get_contents("php://input");
        //这里没有去做回调的判断，可以参考手机做一个判断。
        try {
            $xmlObj = (object)WxPayResults::Init($xml);//这里微信默认返回的数组本人喜欢object
            //$xmlObj= simplexml_load_string($xml); //解析回调数据
            $appid = $xmlObj->appid;//微信appid
            $mch_id = $xmlObj->mch_id;  //商户号
            $nonce_str = $xmlObj->nonce_str;//随机字符串
            $sign = $xmlObj->sign;//签名
            $result_code = $xmlObj->result_code;//业务结果
            $openid = $xmlObj->openid;//用户标识
            $is_subscribe = $xmlObj->is_subscribe;//是否关注公众帐号
            $trace_type = $xmlObj->trade_type;//交易类型，JSAPI,NATIVE,APP
            $bank_type = $xmlObj->bank_type;//付款银行，银行类型采用字符串类型的银行标识。
            $total_fee = (float)abs(round(intval($xmlObj->total_fee) / 100, 2));//订单总金额，单位为分
            $fee_type = $xmlObj->fee_type;//货币类型，符合ISO4217的标准三位字母代码，默认为人民币：CNY。
            $transaction_id = StopAttack("",$xmlObj->transaction_id,$postfilter);//微信支付订单号
            $out_trade_no = StopAttack("",$xmlObj->out_trade_no,$postfilter);;//商户订单号
            $attach = $xmlObj->attach;//商家数据包，原样返回
            $time_end = $xmlObj->time_end;//支付完成时间
            $cash_fee = $xmlObj->cash_fee;
            $return_code = $xmlObj->return_code;
            //下面开始你可以把回调的数据存入数据库，或者和你的支付前生成的订单进行对应了。
            //需要记住一点，就是最后在输出一个success.要不然微信会一直发送回调包的，只有需出了succcess微信才确认已接收到信息不会再发包.
            $this->payed_worker($transaction_id,$out_trade_no,$total_fee);
            echo "SUCCESS";        //请不要修改或删除
        } catch (WxPayException $e) {
            echo "FAIL";

        }
    }

    public function doreturn(){
 //no need
    }

    public function refund($data){
        ini_set('date.timezone','Asia/Shanghai');
        error_reporting(E_ERROR);
        require_once "./trade/wxpay/lib/WxPay.Api.php";
        if(!empty($data["trade_no"]) && !empty($data["refund_amount"])){
            $out_trade_no = $data["trade_no"];
            $total_fee = isset($data["total_amount"])?:$data["refund_amount"];
            $refund_fee = $data["refund_amount"];
            $input = new WxPayRefund();
            $input->SetOut_trade_no($out_trade_no);
            $input->SetTotal_fee($total_fee);
            $input->SetRefund_fee($refund_fee);
            $input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
            $input->SetOp_user_id(WxPayConfig::MCHID);
            $ret = WxPayApi::refund($input);
            if(isset($ret['result_code']) && $ret['result_code'] == 'SUCCESS'){
                exit('退款成功');
            }
        }
    }

}