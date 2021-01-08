<?php
// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------
$cur_template = '/template/'.strval(cookie('think_template')?:'default').'/';
return [
    // 模板路径
    'view_path'       => '.'.$cur_template,
    // 模板文件名分隔符
    'tpl_replace_string' => [
        '__ROOT__'=> '/',
        '__PUBLIC__'=>'/public/',
        '__TMPL__'=> $cur_template,
        '__CSS__'=>  $cur_template.'css/',
        '__JS__'=> $cur_template.'js/',
    ]
];
