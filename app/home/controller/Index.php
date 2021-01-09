<?php
declare (strict_types = 1);

namespace app\home\controller;

use think\facade\Request;
use app\base\model\ArticleView;

class Index extends Base
{

    public function index()
    {
        return $this->display('/index');
    }

}
