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
    protected $title;

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
        $this->head();
    }

    // 初始化
    protected function initialize()
    {
        // 查找所有系统设置表数据
        $abs_path = '/template/' . TMPL_NAME . '/';
        $this->template = '.' . $abs_path;
        config('view.view_path', $this->template);
        config('view.tpl_replace_string.__TMPL__', $abs_path);
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

    public function head()
    {
        //读取数据库和缓存
        $type = M('type');
        $article = M('article');
        $config = config('basic');
        //封装网站配置
        $this->assign('title', $this->title);
        $this->assign('config', $config);
        $this->assign('keywords', $config['sitekeywords']);
        $this->assign('description', $config['sitedescription']);
        if (cookie('think_template') == 'xinwen') {
            //滚动公告
            $data[] = ['status','=',1];
            $data[] = ['typeid','=',$config['noticeid']];
            $roll = $article->where($data)->field('aid,title')->orderRaw('addtime desc')->limit($config['rollnum'])->cache(300)->select()->toArray();
            //处理标题:防止标题过长撑乱页面
            foreach ($roll as $k => $v) {
                $roll[$k]['title'] = msubstr($v['title'], 0, 20, 'utf-8');
            }
            $this->assign('roll', $roll);
        }
        //网站导航
        $all_menu = S('web_menu');
        if(is_array($all_menu) && count($all_menu)==2){
            $menuson = $all_menu[0];
            $menu = $all_menu[1];
        }else{
            $menu = $type->whereRaw('ismenu=1')->orderRaw('drank asc')->select()->toArray();
            foreach ($menu as $k => $v) {
                $menuson[$k] = $type->removeOption()->whereRaw('fid=' . $v['typeid'] . ' AND drank <> 0')->orderRaw('drank asc')->select()->toArray();
                $menu[$k]['submenu'] = $menuson[$k];
            }
            $all_menu = [$menuson,$menu];
            S('web_menu',$all_menu);
        }
        //dd($menu);
        $this->assign('menuson', $menuson);
        $this->assign('menu', $menu);
        //位置导航
        $nav = '<a href="' . $config['siteurl'] . '">首页</a>';
        if ($this->request->param('aid')) {
            $typeid = get_field('article','aid=' . intval($this->request->param('aid')),'typeid');
        } elseif($this->request->param('typeid')) {
            $typeid = intval($this->request->param('typeid'));
        }
        if(!empty($typeid)){
        $typename = get_field('type','typeid=' . $typeid,'typename',60);
        $path = get_field('type','typeid=' . $typeid,'path',60);
        $typelist = explode('-', $path);
        //拼装导航栏字符串
        foreach ($typelist as $v) {
            if ($v == 0) continue;
            $s = get_field('type','typeid=' . $v,'typename',60);
            $nav .= "&nbsp;&gt;&nbsp;<a href=\"" . U('lists/' . $v) . "\">{$s}</a>";
        }
        $nav .= "&nbsp;&gt;&nbsp;<a href=\"" . U('lists/' . $typeid) . "\">{$typename}</a>";
        }
        $this->assign('nav', $nav);
        //释放内存
        unset($type, $article);
        $this->assign('head', TMPL_PATH . TMPL_NAME . '/head.html');
        $this->assign('footer', TMPL_PATH . TMPL_NAME . '/footer.html');
    }

}