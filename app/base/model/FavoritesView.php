<?php
/***********************************************************
[大米CMS] (C)2011 - 2011 damicms.com

@function article 视图模型

@Filename ArticleViewModel.class.php $

@Author 追影 QQ:279197963 $

@Date 2011-11-17 15:22:07 $
 *************************************************************/
namespace app\base\model;

use app\base\model\ViewModel;

class FavoritesView extends ViewModel
{
    public $viewFields = array(
        'favorites'=>array('id','_as'=>'favorites','_type'=>'LEFT'),
        'article'=>array('*', '_as'=>'article' ,'_on'=>'article.aid= favorites.aid'),
    );

}
?>