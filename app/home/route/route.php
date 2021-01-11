<?php
use think\facade\Route;
$cate = \app\base\model\Type::field('typeid,typename,url,islink')->where([['url','<>',''],['islink','=',1]])->cache(600)->select()->toArray();
foreach ($cate as $k => $v) {
            // 当栏目设置了[栏目目录]字段时注册路由
            Route::any($v['url'],  'Lists/index')->append(['typeid'=>$v['typeid']]);
}
Route::rule('articles/:aid', 'Article/index');
Route::rule('lists/:typeid', 'Lists/index');
Route::rule('photos', 'List/photo');
Route::rule('votes/:id', 'Vote/index');
Route::rule('s', 'Index/search');
Route::group('/', function () {
    Route::rule('about', 'Lists/index')->append(['typeid'=>15]);
    Route::rule('news', 'Lists/index')->append(['typeid'=>18]);
    Route::rule('product', 'Lists/index')->append(['typeid'=>22]);
    Route::rule('project', 'Lists/index')->append(['typeid'=>27]);
    Route::rule('zhaopin', 'Lists/index')->append(['typeid'=>25]);
});
