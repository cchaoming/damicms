<?php

namespace app\base\model;

use app\base\model\ViewModel;

class TradeView extends ViewModel
{
    public $viewFields = array(
        'member_trade'=>array('*','_as'=>'member_trade','_type'=>'LEFT'),
        'article'=>array('title','_as'=>'article','typeid','content','price'=>'self_price','product_xinghao','color', '_on'=>'member_trade.gid=article.aid','_type'=>'LEFT'),
        'type'=>array('typename','_as'=>'type','_on'=>'article.typeid=type.typeid'),
    );
}