<?php

namespace app\base\model;

use think\facade\Db;
use think\Model;

class Type extends Model
{
    protected $pk = 'typeid';

    public function Article()
    {
        return $this->hasMany(Article::class, 'typeid', 'typeid');
    }

    function tclm($cid, $fid,$edit=false)
    {
        $substr = get_children($cid, 0);
        if (!$fid || !is_numeric($fid)) {
            $path = '0';
        } else {
            $a = $this->removeOption()->whereRaw('typeid=' . (int)$fid)->field('path,typeid')->find();
            if ($a) {
                $path = $a['path'] . '-' . $a['typeid'];
            } else {
                $path = '0';
            }
        }
        //增加后更新path
        $model = $this->removeOption()->whereRaw('typeid=' . (int)$cid);
        $t = $model->find();
        if ($t) {
            $old_path = $t['path'];
            $model->save(['path' => $path]);
            //修改时注意将自身的子孙path也修改下
            if ($substr != '' && $edit) {
                $prefix = (string)config('database.connections.mysql.prefix');
                $new_str = 'replace(`path`,\'' . $old_path . '-' . $cid . '\',\'' . $path . '-' . $cid . '\')';
                $sql = "update `" . $prefix . "type` set `path`=" . $new_str . " where typeid in(" . $substr . ")";
                Db::execute($sql);
            }
        }
    }
}