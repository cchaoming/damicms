<?php
return [
    // 模板文件名分隔符
    'tpl_replace_string' => [
        '__APP__'=> '/'.array_search("admin",(array)config('app.app_map')).'/',
    ]
];
