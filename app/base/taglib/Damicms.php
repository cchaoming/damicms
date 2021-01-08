<?php
namespace app\base\taglib;
use think\template\TagLib;

class Damicms extends TagLib {

    protected $tags = array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        //大米万能标签
        'arclist' => array('attr' => 'model,where,order,num,id,page,pagesize,pagevar,sql,field,cache,prefix,group,distinct,showpager', 'level' => 3),
        //类别
        'category' => array('attr' => 'parentid,withself,order,id,other', 'level' => 3),
    );

    /*万能标签
        <arclist model='catalog' where='' order='' num='10' page='true' id='vo' pagesize='15' field='' debug='true' table_prefix='true' ></arclist>
        */
    public function tagArclist($attr, $content)
    {
        $html = '';
        $tag = $attr;
        $model = !empty($tag['model']) ? $tag['model'] : 'article';
        $pagevar = !empty($tag['pagevar']) ? $tag['pagevar'] : 'page';
        $order = !empty($tag['order']) ? $tag['order'] : '';
        $group = !empty($tag['group']) ? $tag['group'] : '';
        $num = !empty($tag['num']) ? $tag['num'] : '';
        $id = !empty($tag['id']) ? $tag['id'] : 'vo';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $where = !empty($tag['where']) ? $tag['where'] : '';
        //使where支持 条件判断,添加不等于的判断
        $where = $this->parseCondition($where);
        $page = false;
        if (!empty($tag['page'])) $page = $tag['page'];
        $showpager = !empty($tag['showpager']) ? $tag['showpager'] : true;
        $pagesize = !empty($tag['pagesize']) ? $tag['pagesize'] : '10';
        //是否用缓存,默认是false
        $cache = !empty($tag['cache']) ? $tag['cache'] : false;
        $query = !empty($tag['sql']) ? $tag['sql'] : '';
        $field = !empty($tag['field']) ? $tag['field'] : '';
        $debug = !empty($tag['debug']) ? $tag['debug'] : false;
        $prefix = !empty($tag['prefix']) ? $tag['prefix'] : false;
        $distinct = !empty($tag['distinct']) ? $tag['distinct'] : false;
        //使query 支持条件判断
        $query = $this->parseCondition($query);
        // if($where!='')  $where.=' and '.$flag;
        $className = "\\app\\base\\model\\".ucfirst($model);
        if (class_exists($className)) {
            $html .= '<?php $m=new ' . $className . '();';
        } else {
            if ($prefix == false) {
                $model = config('database.connections.mysql.prefix') . $model;
            } else {
                $model = $tag['model'];
            }
            $html .= '<?php $m=\\think\\facade\\Db::table("' . $model . '");';
        }
        //如果使用了query,将忽略使用where,num,order,page,field,cache 等,使用query无法实现分页
        if ($query) {
            if ($cache != false) {
                $html .= '$cache_key="key_".md5("' . $query . '");';
                $html .= 'if(!$ret=S($cache_key)){ $ret=\think\facade\Db::query("' . $query . '");S($cache_key,$ret);}';
            } else {
                $html .= '$ret=$m->query("' . $query . '");';
            }
        }
        //如果使用了分页,缓存也不生效
        if ($page && !$query) {
            $html .= '$count=$m->whereRaw("' . $where . '")->count();';
            //如果使用了分页，num将不起作用
            $html .= '$ret=$m->distinct(' . $distinct . ')->field("' . $field . '")->whereRaw("' . $where . '")->group("' . $group . '")->orderRaw("' . $order . '")->paginate('.$pagesize.');';
            $html .= '$pagerInfo=$ret->render();';
        }
        //如果没有使用分页，并且没有 query
        if (!$page && !$query) {
            //有缓存
            if ($cache != false) {
                //包含缓存判断
                $html .= '$cache_key="key_".md5($m->Distinct(' . $distinct . ')->field("' . $field . '")->where("' . $where . '")->group("' . $group . '")->order("' . $order . '")->limit("' . $num . '")->select(false));';
                $html .= 'if(!$ret=S($cache_key)){ $ret=$m->distinct(' . $distinct . ')->field("' . $field . '")->whereRaw("' . $where . '")->group("' . $group . '")->orderRaw("' . $order . '")->limit("' . $num . '")->select(); S($cache_key,$ret,' . $cache . '); }';
            } else {
                //没有缓存
                $html .= '$ret=$m->distinct(' . $distinct . ')->field("' . $field . '")->whereRaw("' . $where . '")->group("' . $group . '")->orderRaw("' . $order . '")->limit("' . $num . '")->select();';
            }

        }
        if ($debug != false) {
            $html .= 'dump($ret);dump($m->getLastSql());';
        }
        $html .= 'if(is_array($ret)):$' . $key . ' = 0;';
        $html .= 'foreach($ret as $key=>$' . $id . '):';
        $html .= '++$' . $key . ';?>';
        $html .= $this->tpl->parse($content);
        $html .= '<?php endforeach;endif; ?>';
        if ($page && $showpager) $html .= '<div class="t_page"><?php echo $pagerInfo;?></div>';
        return $html;
    }

    /*文章分类
    <category parentid="0" withself="false" other="" id="vo"></category>
    */
    public function tagCategory($attr, $content)
    {
//把标签的所有属性解析到$tag数组里面
        $tag = $attr;
//得到标签里面的属性
        $parentid = $tag['parentid'];
        $other = isset($tag['other']) ? $tag['other'] : '';
        $order = !empty($tag['order']) ? $tag['order'] : 'drank asc';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $withself = 'false';
        if (!empty($tag['withself'])) $withself = $tag['withself'];
//定义页面解析的变量
        $ret = !empty($tag['id']) ? $tag['id'] : 'vo'; //定义数据查询的结果存放变量
//拼凑输出字符
        $where = '';
        if ($withself == 'false') {
            $where .= "fid={$parentid} and ";
        } else if ($withself == 'true') {
            $where .= "(typeid={$parentid} or fid={$parentid}) and ";
        }
        if ($other != '') {
            $where .= $other . ' and ';
        }
        $where .= '1=1';
        $parsestr = "<?php \$result=\\think\\facade\\Db::name('type')->whereRaw(\"$where\")->orderRaw(\"$order\")->select();";
        $parsestr .= 'if(is_array($result)): $' . $key . ' = 0;';
        $parsestr .= 'foreach($result as $key=>$' . $ret . '):';
        $parsestr .= '++$' . $key . ';?>';
        $parsestr .= $this->tpl->parse($content);
        $parsestr .= '<?php endforeach;endif;?>';
        return $parsestr;
    }

}