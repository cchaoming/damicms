<?php
declare (strict_types = 1);

namespace app\home\controller;

use think\facade\Request;
use app\base\model\ArticleView;

class Index extends Base
{
    protected $title='首页';

    public function index()
    {
        //快捷调用示例：最新新闻
        //parent::lists(18, 0, 9, 'list_new');
        //菜单目录
        //parent::children_dir(22);
        //------------文章系统的东东-------------
        if ( TMPL_NAME == 'xinwen') {
            $config = config('basic');
            $type = M('type');
            $article = M('article');
            //网站公告
            $notice = $article->whereRaw('status=1 AND typeid=' . $config['noticeid'])->field('aid,title')->orderRaw('addtime desc')->limit($config['noticenum'])->cache(600)->select()->toArray();
            $this->assign('notice', $notice);
            unset($notice);
            //首页幻灯内容
            //先模式判断
            if ($config['flashmode'] == 0) {
                $hd = M('flash');
                $hd = $hd->whereRaw('status=1')->orderRaw('`rank` asc')->limit($config['ishomeimg'])->select()->toArray();
                foreach ($hd as $k => $v) {
                    $hd[$k]['imgurl'] = __PUBLIC__ . "/Uploads/hd/" . $v['pic'];
                    if (empty($v['pic'])) {
                        $hd[$k]['imgurl'] = TMPL_PATH . TMPL_NAME . "/images/nopic.png";
                    }
                }
            } else {
                $hd = $article->removeOption()->whereRaw('isflash=1')->field('title,aid,imgurl')->orderRaw('addtime desc')->limit($config['ishomeimg'])->select()->toArray();
                //判断处理图片地址
                foreach ($hd as $k => $v) {
                    $hd[$k]['url'] = U("articles/" . $v['aid']);
                    if (empty($v['imgurl'])) {
                        $hd[$k]['imgurl'] = TMPL_PATH . cookie('think_template') . "/images/nopic.png";
                    }
                }
            }
            $this->assign('flash', $hd);
            unset($flash);
            //首页top 2
            $map[] = ['istop','=',1];
            $map[] = ['ishot','=',1];
            $map[] = ['status','=',1];
            $top = $article->removeOption()->where($map)->field('aid,title,note')->orderRaw('addtime desc')->limit(2)->select()->toArray();
            $top[0]['title'] = msubstr($top[0]['title'], 0, 18, 'utf-8');
            $top[0]['note'] = msubstr($top[0]['note'], 0, 50, 'utf-8');
            $top[1]['title'] = msubstr($top[1]['title'], 0, 18, 'utf-8');
            $top[1]['note'] = msubstr($top[1]['note'], 0, 50, 'utf-8');
            $this->assign('top', $top);
            unset($top, $map);
            //首页栏目内容
            $list = $type->removeOption()->whereRaw('isindex=1')->orderRaw('irank asc')->field('typeid,typename,indexnum')->select()->toArray();
            foreach ($list as $k => $v) {
                $data[] = ['status','=',1];
                $data[] = ['typeid','=',$v['typeid']];
                $k % 2 == 0 ? $list[$k]['i'] = 0 : $list[$k]['i'] = 1;
                //方便定位广告,引入p
                $list[$k]['p'] = $k;
                $list[$k]['article'] = $article->removeOption()->where($data)->orderRaw('addtime desc')->field('title,aid,titlecolor')->limit($v['indexnum'])->select()->toArray();
            }
            $this->assign('list', $list);
            unset($list);
            //首页投票
            $this->vote($config['indexvote']);
            //释放内存
            unset($type, $article);
        }
        return $this->display('/index');
    }
    //ajax目录列表
    function ajax_list_dir()
    {
        $arr = get_files(TMPL_PATH . cookie('think_template') . '/list');
        $this->ajaxReturn($arr, '返回', 1);
    }

    //ajax目录列表
    function ajax_page_dir()
    {
        $arr = get_files(TMPL_PATH . cookie('think_template') . '/page');
        $this->ajaxReturn($arr, '返回', 1);
    }

    public function search()
    {
        if (!$this->request->param('k')) {
            alert('请输入关键字!');
        }
        $article = D('ArticleView');
        $map[] = ['status','=',1];
        $keyword = inject_check(urldecode($this->request->param('k')));
        $keyword = remove_xss($keyword);
        $map[] = ['title','like', '%' . $keyword . '%'];
        if($this->request->param('tag')){
            $map[] = ['keywords','like', '%' . urldecode($this->request->param('tag')) . '%'];
        }
        //导入分页类
        $list = $article->getTableInstance()->where($map)->field("aid,title,addtime,article.typeid,type.typename")->orderRaw("addtime desc")->paginate(10);
        $count = $list->total();
        //保持分页参数
        if ($this->request->isPost()) {
            $allow_par = array('page','k','tag');
            foreach ($this->request->post() as $key => $val) {
                if(in_array($key,$allow_par)){
                    $append[$key] = $val;
                }
            }
            $list->appends($append);
        }
        $items = $list->getCollection()->toArray();
        //封装变量
        foreach($items as $k=>$v){
            array_walk($v,"highlight_keyword",$keyword);
            $items[$k] = $v;
        }
        $this->assign('list', $items);
        $this->assign('num', $count);
        $this->assign('page', $list->render());
        $this->assign('keyword', $keyword);
        //模板输出
        return $this->display( '/search');
    }

    //调查模块
    private function vote($isvote)
    {
        $vote = M('vote');
        $vo = $vote->whereRaw('id=' . intval($isvote))->find();
        if ($vo) {
            $strs = explode("\n", trim($vo['vote']));
            for ($i = 0; $i < count($strs); $i++) {
                $s = explode("=", $strs[$i]);
                $data[$i]['num'] = $s[1];
                $data[$i]['title'] = $s[0];
            }
        } else {
            $vo['title'] = '投票ID不存在!请检查网站配置!';
        }
        $this->assign('vtype', $vo['stype']);
        $this->assign('vid', $isvote);
        $this->assign('vtitle', $vo['title']);
        $this->assign('vdata', $data);
    }

    //申请友链
    public function reglink()
    {
        $this->display( '/reglink');
    }

    public function doreglink()
    {
        header("Content-type: text/html; charset=utf-8");
        $link = M('link');
        $check = $this->request->checkToken(config('app.TOKEN_NAME'));
        if(false === $check) {
            $this->error('invalid token');
        }
        $data['title'] = remove_xss(htmlentities($_POST['Title']));
        $data['url'] = remove_xss(htmlentities($_POST['LinkUrl']));
        $data['logo'] = remove_xss(htmlentities($_POST['LogoUrl']));
        $data['status'] = 0;
        $data['rank'] = 10;
        if ($link->add($data)) {
            echo "<br/><br/><br/><font color=red>添加成功，等待审核！请在贵站加上本站链接。</font>";
        } else {
            echo "<br/><br/><br/><font color=red>添加失败,请联系管理员!</font>";
        }

    }
}
