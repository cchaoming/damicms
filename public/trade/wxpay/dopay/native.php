<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.NativePay.php";
require_once 'log.php';
$path = str_replace(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']),'',str_replace('\\','/',dirname(__FILE__)));
$out_trade_no =  $_REQUEST['WIDout_trade_no'];
$subject = $_REQUEST['WIDsubject'];
$total_fee = intval(floatval($_REQUEST['WIDtotal_fee'])*100);//付款金额
$body = $_REQUEST['WIDbody']; //订单描述
if($out_trade_no ==''){echo 'param error!';exit();}
//模式一
/**
 * 流程：
 * 1、组装包含支付信息的url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、确定支付之后，微信服务器会回调预先配置的回调地址，在【微信开放平台-微信支付-支付配置】中进行配置
 * 4、在接到回调通知之后，用户进行统一下单支付，并返回支付信息以完成支付（见：native_notify.php）
 * 5、支付完成之后，微信服务器会通知支付成功
 * 6、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
$notify = new NativePay();
//$url1 = $notify->GetPrePayUrl("123456789");

//模式二
/**
 * 流程：
 * 1、调用统一下单，取得code_url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、支付完成之后，微信服务器会通知支付成功
 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
$input = new WxPayUnifiedOrder();
$input->SetBody($subject);
$input->SetAttach($body);
$input->SetOut_trade_no($out_trade_no);
$input->SetTotal_fee($total_fee);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 3600));
$input->SetGoods_tag("test");
$input->SetNotify_url(WxPayConfig::NOTIFY_URL);
$input->SetTrade_type("NATIVE");
$input->SetProduct_id(time());
//var_dump($input);
$result = $notify->GetPayUrl($input);
$url2 = $result["code_url"];
if($url2 ==''){echo '微信返回参数错误error!';exit();}
$brr = explode("-", $out_trade_no);
			if(count($brr)>=3){
			 $out_trade_no = $brr[0].'-'.$brr[1];	
			}
?>

<html>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>微信扫码支付</title>
    <link rel="stylesheet" href="http://www.damicms.com/Public/Css/wxpay.css">
    <script src="http://www.damicms.com/Public/js/jquery.js"></script>
</head>
<body style="padding: 50px;background-color: rgb(51, 51, 51);">

<div class="main impowerBox">
    <div class="loginPanel normalPanel">
        <div class="title">微信扫码支付</div>
        <div class="waiting panelContent">
            <div class="wrp_code"><img class="qrcode lightBorder" alt="微信扫码支付" src="<?php echo $path;?>/qrcode.php?data=<?php echo urlencode($url2);?>" /></div>
            <div class="info">
                <div class="status status_browser js_status normal" id="wx_default_tip">
                    <p>请使用微信扫描二维码支付</p>
                    <p id="pay_result">“微支付”</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var iTime = 2*3600; //2小时
    var Account;
    function RemainTime() {
        // alert(iTime);
        if (iTime > 0) {
            Account = setTimeout("RemainTime()", 1000);
            iTime = iTime - 1;
        }
        else {
            clearTimeout(Account);
            return false;
        }
//调用微信查询
        pf_wxQuery('<?php echo $out_trade_no;?>');

    }

    //根据订单号查询是否已支付成功
    function pf_wxQuery(out_trade_no)
    {
        $.getJSON("/index.php?m=Api&a=wx_query&out_trade_no=" + out_trade_no ,function(result){
            if(result.status == 1){
                $('#pay_result').html('√支付成功');
                setTimeout(function () {
                    location.href='/index.php?m=member&a=buylist';
                }, 4000);
            }
        });
    }
	RemainTime();
</script>

</body>
</html>