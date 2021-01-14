<?php
namespace app\base\validate;

use think\Validate;

class ExtendFieldes extends Validate
{
    protected $rule = [
        'field_type'  => 'require',
        'field_name'=>'unique:extend_fieldes'
    ];

    protected $message  =   [
        'field_type.require' => '扩展类型必须',
        'field_name.unique'     => '字段名已经存在',
    ];

}