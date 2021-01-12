<?php
$alipay_config =[];
require_once("alipay.config.php");
require_once("lib/alipay_submit.class.php");
$trade_no =  $_POST['trade_no'];//支付宝的订单号
$reason =  $_POST['reason'];
$refund_amount  = $_POST['refund_amount'];
        //批次号，必填，格式：当天日期[8位]+序列号[3至24位]，如：201603081000001
        //$batch_no = $_POST['WIDbatch_no'];
        $batch_no = date('YmdHis');
        $batch_num = 1;
        //退款详细数据，必填，格式（支付宝交易号^退款金额^备注），多笔请用#隔开
        //$detail_data = $_POST['WIDdetail_data'];
        $detail_data = $trade_no.'^'.$refund_amount.'^'.$reason;
        //构造要请求的参数数组，无需改动
        $parameter = array(
		"service" => 'refund_fastpay_by_platform_pwd',
		"partner" => trim($alipay_config['partner']),
		"notify_url"	=> "http://" . $_SERVER['HTTP_HOST'].'/index.php/Public/notify/code/alipay',
		"seller_user_id"	=> $alipay_config['partner'],
		"refund_date"	=> trim(date('Y-m-d H:i:s')),
		"batch_no"	=> $batch_no,
		"batch_num"	=> $batch_num,
        'sign_type' => 'MD5',
		"detail_data"	=> $detail_data,
		"_input_charset"	=> 'utf-8'
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($this->alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
           echo $html_text;
