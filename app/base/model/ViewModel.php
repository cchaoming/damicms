<?php

namespace app\base\model;
use think\facade\Db;
class ViewModel
{
    protected $viewFields = [];
    private $viewTableName;

    public function getTableInstance() {
        if(empty($this->viewTableName)) {
            $i=0;
            foreach ($this->viewFields as $key=>$view){
                // 获取数据表名称
                $fields = !empty($view[0])?$view[0]:'*';
                $as = !empty($view['_as'])?$view['_as']:'';
                $table_name = $as?$key.' '.$as:ucfirst($key);//别名
                $on =  !empty($view['_on'])?$view['_on']:'';
                if($i==0){
                    $tableName = Db::view($table_name,$fields);
                    $type = !empty($view['_type'])?$view['_type']:'INNER';
                }else{
                    if(!empty($type) && $on){
                      $tableName->view($table_name,$fields,$on,$type);
                      $type = !empty($view['_type'])?$view['_type']:'INNER';
                    }
                }
                $i++;
            }
            $this->viewTableName    =   $tableName;
        }
        return $this->viewTableName;
    }
}