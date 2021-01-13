<?php
namespace app\base\model;
use think\Model;

class General extends Model{
    public function __construct(array $data = [],$table='',$pk='id')
    {
        $this->name= $table;
        $this->pk = $pk;
        parent::__construct($data);
    }

}
