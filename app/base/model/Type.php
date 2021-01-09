<?php

namespace app\base\model;

use think\Model;

class Type extends Model
{
    protected $pk = 'typeid';

    public function Article()
    {
        return $this->hasMany(Article::class,'typeid','typeid');
    }

}