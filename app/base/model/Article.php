<?php

namespace app\base\model;

use think\Model;

class Article extends Model
{
    public function type()
    {
        return $this->hasOne(Type::class,'typeid','typeid');
    }
}