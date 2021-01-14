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

    public function pl(){
        return $this->hasMany(Pl::class,'aid','aid');
    }

    public function mood(){
        return $this->hasOne(Mood::class,'aid','aid');
    }

}