<?php
declare (strict_types = 1);

namespace app\admin\controller;

class Index
{
    public function index()
    {
        echo MODULE_NAME.'<br>';
        echo ACTION_NAME.'<br>';
    }
}
