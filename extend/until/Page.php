<?php
//已改写 for 前台分页 追影 QQ:279197963 2011年11月14日 18:05:23
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$
namespace until;
use think\facade\Request;

class Page {
    // 起始行数
    public $firstRow	;
    // 列表每页显示行数
    public $listRows	;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    public $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页栏每页显示的页数
    protected $rollPage   ;
	//默认分页变量
	protected $varPage;
	// 分页显示定制
    protected $config  =	['header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%'];

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     +----------------------------------------------------------
     */
    public function __construct($totalRows,$listRows,$parameter='',$var_page= 'p') {
		$this->varPage = $var_page;
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->rollPage = 5;
        $this->listRows = !empty($listRows)?$listRows:20;
        $this->totalPages = ceil((int)$this->totalRows / (int)$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = (int)Request::param($this->varPage,1);
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     +----------------------------------------------------------
     * 分页显示输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p = $this->varPage;
		$ext_htm = '.'.config('route.url_html_suffix');
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
		//静态化需要
		$url = $_SERVER['REQUEST_URI'];
		$url = str_ireplace($ext_htm,'',$url);
		$url = preg_replace("/(#.+$|(\/)+".$p."\/[0-9]+)/i",'',$url);	
		if($this->parameter != ''){
		$url  =  $url.(strpos($_SERVER['REQUEST_URI'],'?')?'':"/").$this->parameter;		
		}
        /*$parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }*/
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            $upPage="<a rel='external' href='".$url."/".$p."/$upRow{$ext_htm}'>".$this->config['prev']."</a>\n";
        }else{
            $upPage="<span>".$this->config['prev']."</span>\n";
        }

        if ($downRow <= $this->totalPages){
            $downPage="<a rel='external' href='".$url."/".$p."/$downRow{$ext_htm}'>".$this->config['next']."</a>\n";
        }else{
            $downPage="<span>".$this->config['next']."</span>\n";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            $prePage = "<a href='".$url."/".$p."/$preRow' >上".$this->rollPage."页</a>";
            $theFirst = "<a href='".$url."/".$p."/1' >".$this->config['first']."</a>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<a href='".$url."/".$p."/$nextRow' >下".$this->rollPage."页</a>";
            $theEnd = "<a href='".$url."/".$p."/$theEndRow' >".$this->config['last']."</a>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $linkPage .= "<a rel='external' href='".$url."/".$p."/$page{$ext_htm}'>".$page."</a>\n";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .="<span>".$page."</span>\n";
                }
            }
        }
		//下拉选择分页效果
		$allPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $allPage .="<option value='".$url."/".$p."/$page{$ext_htm}'>".$page."</option>";
                }else{
                    break;
                }
            }else{
                    $allPage .="<option value='".$url."/".$p."/$page{$ext_htm}' selected='selected'>".$page."</option>";
            }
        }
        $pageStr	 =	 str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%allPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$allPage,$nextPage,$theEnd),$this->config['theme']);
        return $pageStr;
    }

}
?>