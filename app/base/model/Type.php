<?php

namespace app\base\model;

use think\Model;

class Type extends Model
{
    public function Articles()
    {
        return $this->hasMany(Article::class,'typeid','typeid');
    }

}