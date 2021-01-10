<?php
declare (strict_types = 1);

namespace app\home\controller;

use app\base\model\ArticleView;
use until\Page;

class Article extends Base
{
    public function index()
    {
        $aid = intval($this->request->param('aid',0));
        if (!$aid) {
            $this->error('非法操作');
        }
        $article = M('article');
        $config = config('basic');
        $page_model = 'page/page_default.html';
        //相关判断
        $alist = $article->where('aid=' . $aid)->find();
        if (!$alist) {
            alert('文章不存在或已删除!');
        }
        if ($alist['islink'] == 1) {
            Header('Location:' . $alist['linkurl']);
        }
        if ($alist['status'] == 0) {
            alert('文章未审核!');
        }
        //阅读权限
        if ($config['isread'] == 1) {
            $uvail = explode(',', session('dami_uservail'));
            if (!in_array($alist['typeid'], $uvail)) {
                alert('对不起您没有阅读改文章的权限！');
            }
        }
        $this->assign('title', $alist['title']);
        if ($alist['keywords'] != "") {
            $this->assign('keywords', $alist['keywords']);
        }
        if ($alist['description'] != "") {
            $this->assign('description', $alist['description']);
        }
        parent::tree_dir($alist['typeid'], 'tree_list');
        $type = M('type');
        $list = $type->whereRaw('typeid=' . intval($alist['typeid']))->find();
        if ($list) {
            $pid = get_first_father($list['typeid']);
            $cur_menu = get_field('type', 'typeid=' . $pid, 'drank');
            $this->assign('cur_menu', $cur_menu);
            $this->assign('type', $list);
        }
        $a = isset($list['page_path'])?$list['page_path']:'';
        if ($a != '' && file_exists($this->template . '/' . $a)) {
            $page_model = $a;
        }
        //统计处理
        if ($alist['status'] == 1) {
            $map['hits'] = $alist['hits'] + 1;
            $article->removeOption()->whereRaw('aid=' . $aid)->save($map);
        }
//注销map
        unset($map);
        $alist['hits'] += 1;
//关键字替换
        $alist['content'] = $this->key($alist['content']);
//鼠标轮滚图片
        if ($config['mouseimg'] == 1) {
            $alist['content'] = $this->mouseimg($alist['content']);
        }
//文章内分页处理
        if (intval($alist['pagenum']) <= 0) {
//手动分页
            $alist['content'] = $this->diypage($alist['content']);
        } else {
//自动分页
            $alist['content'] = $this->autopage($alist['pagenum'], $alist['content']);
        }
//文章内投票
        if(intval($alist['voteid'])>0){
        $this->vote($alist['voteid']);
        }
//心情投票
        $url = $this->request->root();//用于心情js的根路径变量
        $this->assign('url', $url);
        //文章上下篇
        $map[] = ['status','=',1];
        $map[] = ['typeid','=',$alist['typeid']];
        $rows = $article->removeOption()->where($map)->field('aid,title')->orderRaw('istop desc,addtime desc')->select()->toArray();
        $arr_aid = array();
        foreach ($rows as $row) {
            $arr_aid[] = $row['aid']; //取出每一行中的指定的列
        }
        $cur_key = array_search($aid, $arr_aid);
        if (isset($arr_aid[$cur_key + 1])) {
            $up = $article->removeOption()->whereRaw('aid=' . intval($arr_aid[$cur_key + 1]))->find();
            $up['title'] = msubstr($up['title'], 0, 20, 'utf-8');
            $lastpage = '<a href="' . U('articles/' . $up['aid']) . '" data-icon="arrow-l" data-iconpos="left">' . $up['title'] . '</a>';
            $updown = '下一篇：<span><a href="' . U('articles/' . $up['aid']) . '" >' . $up['title'] . '</a></span>';
        } else {
            $lastpage = '';
            $updown = '下一篇：<span>无</span>';
        }
        $this->assign('lastpage', $lastpage);

        if (isset($arr_aid[$cur_key - 1])) {
            $down = $article->removeOption()->whereRaw('aid=' . intval($arr_aid[$cur_key - 1]))->find();
            $dowm['title'] = msubstr($down['title'], 0, 20, 'utf-8');
            $nextpage = '<a href="' . U('articles/' . $down['aid']) . '" data-icon="arrow-r" data-iconpos="right">' . $down['title'] . '</a>';
            $updown .= '  上一篇：<span><a href="' . U('articles/' . $down['aid']) . '">' . $down['title'] . '</a></span>';
        } else {
            $nextpage = '';
            $updown .= '  上一篇：<span>无</span>';
        }
        $this->assign('nextpage', $nextpage);
        $this->assign('updown', $updown);
        //释放相关内存
        unset($rows, $updown, $up, $down, $map, $lastpage, $nextpage);
        //相关文章
        if ($alist['keywords'] != '') {
            $relate = $article->removeOption()->where('status','=',1);
            $keywords = explode(",", $alist['keywords']);
            foreach ($keywords as $k => $v) {
                if ($k == 0) {
                    $relate = $relate->where('keywords','like',"%{$v}%");
                } else {
                    $relate = $relate->whereOr('keywords','like',"%{$v}%");
                }
            }
            $klist = $relate->field('aid,title,imgurl,addtime')->limit(6)->select()->toArray();
            //封装变量
            $this->assign('keylist', $klist);
        }
        $this->assign('article', $alist);
//释放内存
        unset($article, $alist, $klist, $map);
//模板输出
        return $this->display($this->template. $page_model);
    }

    //投票模块
    private function vote($id)
    {
        $vote = M('vote');
        $vo = $vote->whereRaw('id=' . $id)->find();
        if ($vo) {
            $strs = explode("\n", trim($vo['vote']));
            for ($i = 0; $i < count($strs); $i++) {
                $s = explode('=', $strs[$i]);
                $data[$i]['num'] = $s[1];
                $data[$i]['title'] = $s[0];
            }
        } else {
            $vo['title'] = '投票ID不存在!请检查网站配置!';
        }
        //封装变量
        $this->assign('votetype', $vo['stype']);
        $this->assign('voteid', $vo['id']);
        $this->assign('votetitle', $vo['title']);
        $this->assign('votedata', $data);
        //释放内存
        unset($vote, $vo, $data);
    }


//关键字替换
    private function key($content)
    {
        $key = M('key');
        $keys = $key->field('url,title,num')->select()->toArray();
        $map = [];
        foreach ($keys as $k => $v) {
            $map[$k]['Key'] = $v['title'];
            $map[$k]['Href'] = "<a href=\"{$v['url']}\" target=\"_blank\">{$v['title']}</a>";
            $map[$k]['ReplaceNumber'] = $v['num'];
        }
        $a = new \until\KeyReplace($map, $content);
        $a->KeyOrderBy();
        $a->Replaces();
        return $a->HtmlString;
    }

    //鼠标鼠标滚轮控制图片大小的函数
    private function mouseimg($content)
    {
        $pattern = "/<img.*?src=(\".*?\".*?\/)>/is";
        $content = preg_replace($pattern, "<img src=\${1} onload=\"javascript:resizeimg(this,575,600)\">", $content);
        return $content;
    }

    //文章内容分页-自定义分页
    private function diypage($content)
    {
        $str = explode('[dami_page]', $content);
        $num = count($str);
        if ($num == 1) {
            return $content;
            exit;
        }
        $p = new Page($num, 1);
        $p->setConfig('prev', '上一页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%upPage%%linkPage%%downPage%");
        $this->assign('page', $p->show());
        $this->assign('nowpage', $p->nowPage);
        $nowpage = $p->nowPage - 1;
        //释放内存
        unset($p);
        return $str[$nowpage];
    }


    //文章自动分页
    private function autopage($pagenum, $content)
    {
        if ($pagenum == 0) {
            return $content;
        }
        if (strlen($content) < $pagenum) {
            return $content;
        }
        //导入分页类和函数库
        $num = ceil(strlen($content) / $pagenum);
        $p = new Page($num, 1);
        $p->setConfig('prev', '上一页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%upPage%%linkPage%%downPage%");
        $this->assign('page', $p->show());
        $this->assign('nowpage', $p->nowPage);
        $content = msubstr($content, ($p->nowPage - 1) * $pagenum, $pagenum);
        //释放内存
        unset($p);
        return $content;
    }
}