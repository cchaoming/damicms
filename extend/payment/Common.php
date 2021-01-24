<?php
namespace payment;
class Common{

    protected  function payed_worker($third_trade_no,$out_trade_no,$total_fee){
        $arr = explode("-", $out_trade_no);
        if(count($arr)>=3){
            $out_trade_no = $arr[0].'-'.$arr[1];
        }
        $tlog = M('trade_log');
        $tdail = $tlog->whereRaw("taobao_no='{$third_trade_no}'")->find();
        if (!$tdail) {
            M('member_trade')->whereRaw("out_trade_no='{$out_trade_no}' or group_trade_no='{$out_trade_no}'")->save(['status'=>3]);
            $trade_type = substr($out_trade_no, 0, 2);
            if ($trade_type == "CZ") {
                $uid = intval($arr[1]);
                if ($uid > 0) {
                    M('member')->where('id=' . $uid)->inc('money', $total_fee)->update();
                    //logResult(M('dami_common_member',null)->getLastSql().'<BR>');
                    $data['uid'] = $uid;
                    $data['addtime'] = time();
                    $data['price'] = $total_fee;
                    $data['trade_no'] = $out_trade_no;
                    $data['remark'] = "用户充值";
                    $data['log_type'] = 0;
                    M('money_log')->save($data);
                }
            }
            $taobao_arr['taobao_no'] = $third_trade_no;
            $taobao_arr['trade_no'] = $out_trade_no;
            $taobao_arr['uid'] = $uid;
            $taobao_arr['money'] = $total_fee;
            $taobao_arr['addtime'] = date('Y-m-d H:i:s');
            M('trade_log')->save($taobao_arr);
        }

    }

}