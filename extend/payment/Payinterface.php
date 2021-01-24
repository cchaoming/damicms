<?php
namespace payment;
interface Payinterface{
    public function gateway($data);
    public function donotify();
    public function doreturn();
    public function refund($data);
}