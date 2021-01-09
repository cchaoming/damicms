<?php
declare (strict_types=1);

namespace app\home\controller;

use app\base\model\ArticleView;
use app\home\middleware\ControllerBefore;
use think\App;
use app\BaseController;

/**
 * 控制器基础类
 */
class Base extends BaseController
{
    protected $template;       //模板目录
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [ControllerBefore::class];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        // 查找所有系统设置表数据
        $this->template = './template/' . TMPL_NAME . '/';
        config('view.view_path', $this->template);
        //获取访客权限
        if (intval(cookie('first_look')) != 1 && !session('dami_uid')) {
            if (config('basic.isread') == 1) {
                $group_vail = get_field('member_group', 'group_id=' . config('basic.defaultup'), 'group_vail');
                seesion('dami_uservail', $group_vail);
            }
            cookie('first_look', 1);
        }
    }

    //是否使用静态
    protected function html_init($static_file, $php_file, $is_build)
    {
        clearstatcache();
        if (file_exists($static_file) && $is_build == 1 && session('is_rebuild') != 1 && (time() - filemtime($static_file)) <= 21600 && filemtime($php_file) - filemtime($static_file) <= 0) //判断源文件已修改//缓存6小时 永久的化去掉他
        {
            //View::fetch($static_file); 用这句容易产生注入漏洞因为静态页里的PHP代码仍然解析
            $fp = fopen($static_file, "r");
            $conStr = fread($fp, filesize($static_file));
            fclose($fp);
            echo $conStr;
            exit();
        }
    }

    protected function lists($typeid, $mode, $limit, $param, $order = 'addtime desc')
    {
        //查询数据库
        $article = new ArticleView();
        //封装条件
        $map[] = ['status', '=', 1];
        //准备工作
        //追影 QQ:279197963修改让其支持无限极
        $arr = get_children($typeid);
        //模式判断
        switch ($mode) {
            case 0:
                $map[] = ['article.typeid', 'in', $arr];
                break;
            case 1:
                $map[] = ['article.typeid', '=', $typeid];
                break;
            default:
                $map[] = ['article.typeid', 'in', $arr];
                break;
        }
        $alist = $article->getTableInstance()->where($map)->orderRaw($order)->limit($limit)->select();
        $this->assign($param, $alist);
    }

    //获取子孙目录
    protected function children_dir($typeid = 22, $assign = 'product_dir', $show_all = 0)
    {
        $dao = M('type');
        $t = $dao->where('typeid =' . $typeid)->find();
        if ($t) {
            if ($show_all == 1) {
                $str = $t["path"] . "-" . $t["typeid"];
                $mylist = $dao->where("1 = instr(path,'" . $str . "')")->select();
            } else {
                $mylist = $dao->where("fid = " . $t["typeid"])->select();
            }
            $this->assign($assign, $mylist);
        }
    }

//获取家族树
    protected function tree_dir($typeid, $assign = 'tree_list')
    {
        if ($typeid == 0) {
            exit();
        }
        $str = get_all_tree($typeid);
        $dao = M('type');
        $t = $dao->where('typeid in(' . $str . ')')->order('typeid')->select();
        $this->assign($assign, $t);
    }

}