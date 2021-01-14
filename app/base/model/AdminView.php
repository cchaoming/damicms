<?php
/***********************************************************
[大米CMS] (C)2011 - 2011 damicms.com

@function 管理员视图模型 Model

@Filename AdminViewModel.class.php $

@Author 追影 QQ:279197963 $

@Date 2011-11-17 15:23:44 $
 *************************************************************/
namespace app\base\model;
use app\base\model\ViewModel;

class AdminView extends ViewModel
{
    protected $viewFields = [
        'admin' => ['id,username,lastlogintime,lastloginip,status','_as'=>'admin','_type'=>'LEFT'],
        'role_admin' => ['role_id', '_as'=>'role_admin','_on' => 'admin.id=role_admin.user_id'],
   ];
}
?>