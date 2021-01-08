<?php

namespace app\base\model;

use app\base\model\ViewModel;

class ArticleView extends ViewModel
{
    protected $viewFields = [
        'article' => ['*', '_as'=>'article','_type' => 'LEFT'],
        'type' => ['typename', '_as'=>'type','_on' => 'article.typeid=type.typeid'],
    ];
}