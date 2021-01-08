<?php
use think\facade\Route;
$cate = \app\base\model\Type::field('typeid,typename,url,islink')->where([['url','<>',''],['islink','=',1]])->cache(600)->select();
foreach ($cate as $k => $v) {
            // 当栏目设置了[栏目目录]字段时注册路由
            Route::any($v['url'],  'List/index?typeid='.$v['typeid']);
}
Route::rule('article/<id>', 'Index/index')->append(['app_id' => 1, 'status' => 1]);