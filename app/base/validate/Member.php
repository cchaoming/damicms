<?php
namespace app\base\validate;

use think\Validate;

class Member extends Validate
{
    protected $rule = [
        'username|用户名'  => 'require|max:20|unique:member',
        'userpwd|密码'   => 'require',
        'userpwd2|重复密码'   => 'require|confirm:userpwd',
        'email|邮箱' => 'email',
        'tel|手机'=>'unique:member'
    ];

    protected $message  =   [
        'username.require' => '用户名必须',
        'username.max'     => '用户名不能超过20个字符',
        'userpwd2.confirm'   => '重复密码输入不一致',
        'tel.unique'  => '手机号已经存在',
        'email'        => '邮箱格式错误',
    ];

}