<?php
declare (strict_types=1);

namespace app\home\controller;
use think\App;
use app\BaseController;
use think\facade\Db;

/**
 * 控制器基础类
 */
class Api extends BaseController
{
    //产品相册列表
    function list_product_photo(){
        $list = M('article')->whereRaw("imgurl<>'' and is_from_mobile=1")->field('aid,title,content,imgurl')->select()->toArray();
        $this->ajaxReturn($list,'产品列表',1);
    }
//发送消息
    function domess(){
        $company_id = intval($_REQUEST['companyid']);
        $to_uid = intval($_REQUEST['touid']);
        $content = $_REQUEST['content'];
        $client_tel = $_REQUEST['clienttel'];
        cookie('client_tel',$client_tel);
        $url = config('app.SERVER_URL')."Public/doshop_mess?company_id={$company_id}&to_uid={$to_uid}&content={$content}&client_tel={$client_tel}&from_url=".urlencode('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
        $res = json_decode(file_get_contents($url));
        exit($res->data);
    }
//万能获取数据接口
    function ajax_arclist(){
        $prefix = isset($_REQUEST['prefix'])?(bool)$_REQUEST['prefix']:true;
        //表过滤防止泄露信息,只允许的表
        if(!in_array($_REQUEST['model'],array('article','type','ad','label','link'))){exit();}
        $model = $this->request->param('model', 'article');
        $order     =!empty($_REQUEST['order'])?inject_check($_REQUEST['order']):'';
        $num       =!empty($_REQUEST['num'])?inject_check($_REQUEST['num']):'';
        $where     =!empty($_REQUEST['where'])?inject_check(urldecode($_REQUEST['where'])):'';
        //使where支持 条件判断,添加不等于的判断
        $page=false;
        if(!empty($_REQUEST['page'])) $page=(bool)$_REQUEST['page'];
        $pagesize  =!empty($_REQUEST['pagesize'])?intval($_REQUEST['pagesize']):'10';
        //$query     =!empty($_REQUEST['sql'])?$_REQUEST['sql']:'';//太危险不用
        $field     = '';
        if(!empty($_REQUEST['field'])){
            $f_t = explode(',',inject_check($_REQUEST['field']));
            $f_t = array_map('urlencode',$f_t);
            $field = implode(',',$f_t);
        }
        $className = "\\app\\base\\model\\".ucfirst($model);
        if (class_exists($className)) {
            $m = new $className();
        } else {
            if ($prefix == false) {
                $model = config('database.connections.mysql.prefix') . $model;
                $m = Db::table($model);
            } else {
                $m = Db::name($model);
            }
        }
        //如果使用了分页,缓存也不生效
        if($page){
            $count=$m->where($where)->count();
            $total_page = ceil($count / $pagesize);
            $p = new \until\Page($count,$pagesize);
            //如果使用了分页，num将不起作用
            $t=$m->removeOption();
            if($field){$t->field($field);}
            if($where){$t->whereRaw($where);}
            if($order){$t->order($order);}
            $t->limit($p->firstRow,$p->listRows);
            //echo $m->getLastSql();
            $ret = array('total_page'=>$total_page,'data'=>$t->select()->toArray());
        }
        //如果没有使用分页，并且没有 query
        if(!$page){
            $ret=$m->removeOption();
            if($field){$ret->field($field);}
            if($where){$ret->whereRaw($where);}
            if($order){$ret->order($order);}
            if($num){$ret->limit((int)$num);}
            $ret = $ret->toArray();
        }
        $this->ajaxReturn($ret,'返回信息',1);
    }

//登陆信息js
    function login_js(){
        $ret = '';
        if(session('dami_username')){
            $ret .= '欢迎您，'.session('dami_username').',&nbsp;';
            if(session('dami_usericon')){
                $ret .= '<img src="'.session('dami_usericon').'" border="0" width="16" height="16">';
            }
            $ret .= '<a href="'.U('Member/main').'">会员中心</a>&nbsp;&nbsp;<a href="'.U('Member/dologout').'">退出</a>';
        }
        else{
            if(intval(config('app.QQ_LOGIN')) ==1){
                $ret .= '<a href="'.U('Member/qqlogin').'"><img src="'.TMPL_PATH.TMPL_NAME.'/images/qq_login.png" border="0" width="24" height="24" /></a>';
            }
            if(intval(config('app.WX_LOGIN')) ==1){
                $ret .= '<a href="'.U('Member/wxlogin').'"><img src="'.TMPL_PATH.TMPL_NAME.'/images/wx_login.png" border="0" width="24" height="24" /></a>';
            }
            $ret .= '<a href="'.U('Member/login').'">登陆</a> | <a href="'.U('Member/register').'">注册</a>';
        }
        exit('document.writeln(\''.$ret.'\');');
    }
//文章浏览次数js
    function hits_js()
    {
        $aid = intval($_REQUEST['aid']);
        $field = $this->request->param('type',0)==0?'hits':'good_tp';
        $hits = get_field('article','aid=' . $aid,$field);
        if (intval(config('app.IS_BUILD_HTML')) == 1 && $field== 'hits') {
            M('article')->where('aid',$aid)->inc('hits',1)->update();
        }
        $this->ajaxReturn($hits,1,'','html');
    }
//ajax点赞
    public function ajax_dianzhan(){
        $aid = intval($_REQUEST['aid']);
        $is_yuyue = cookie('dianzhan_'.$aid);
        if(!$is_yuyue){
            M('article')->whereRaw('aid='.$aid)->inc('good_tp',1)->update();
            cookie('dianzhan_'.$aid,1,3600*24);//设置一天有效期
            $ret=1;
        }
        else{
            $ret = 0 ;
        }
        $this->ajaxReturn($ret,1,'','html');
    }
//订单状态
    public function wx_query(){
        $out_trade_no = $this->request->get('out_trade_no');
        $s = M('member_trade')->whereRaw("out_trade_no=:out_trade_no or group_trade_no=:group_trade_no",['out_trade_no'=>$out_trade_no,'group_trade_no'=>$out_trade_no])->value('status');
        if(intval($s) == 3){
            $this->ajaxReturn('success','返回信息',1);
        }else{
            $this->ajaxReturn('fail','返回信息',0);
        }
    }
}