<?php

namespace app\base\model;

use think\Model;

class Article extends Model
{
    protected $pk = 'aid';

    public function type()
    {
        return $this->hasOne(Type::class,'typeid','typeid');
    }
}