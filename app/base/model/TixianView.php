<?php


namespace app\base\model;

use app\base\model\ViewModel;

class TixianView extends ViewModel
{
    protected $viewFields = array(
        'tixian'=>array('*','_as'=>'tixian','_type'=>'LEFT'),
        'member'=>array('id as uid,username,realname,tel,money as have_money,address,province,city,area,email,qq,sex', '_as'=>'member','_on'=>'tixian.uid=member.id'),
    );

}