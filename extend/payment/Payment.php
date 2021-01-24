<?php
namespace  payment;
use think\exception\ClassNotFoundException;

class Payment implements  Payinterface {
    protected $pay;
    public function __construct($payname)
    {
        try{
            $payname = '\\payment\\'.ucfirst($payname);
            $this->pay = new $payname();
        }catch (ClassNotFoundException $e){
            app()->log->record($e->getMessage(), 'error');
        }
    }
    public function gateway($data){
        $this->pay->gateway($data);
    }
    public function donotify(){
        $this->pay->donotify();
    }
    public function doreturn(){
        $this->pay->doreturn();
    }
    public function refund($data){
        $this->pay->refund($data);
    }
}