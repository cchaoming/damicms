<?php
/**
 * 微信公众平台操作
 * 基本于PHP-CURL
 */
namespace until;
class Weixin
{

    /**
     * PHP curl头部分
     * @var array
     */
    private $_header;
	private $appid;
	private $appkey;
    /**
     * 令牌
     * @var string
     */
    public $access_token;
     /**
     * 网址url绑定的令牌
     * @var string
     */
    public $url_token = '';
    /**
     * 是否debug的状态标示，方便我们在调试的时候记录一些中间数据
     * @var boolean
     */
    public $debug =  false;

    public $setFlag = false;
    /**
     *('text','image','location')
     * @var string
     */
    public $msgtype = 'text';
    /**
     *消息信息
     * @var array
     */
    public $msg = array();
	
	public $event;
	
    /**
     * 初始化，设置header
     */
    public function __construct($appid, $appkey,$debug)
    {
        $this->_header = array();
        $this->_header[] = "Host:mp.weixin.qq.com";
        $this->_header[] = "Referer:https://mp.weixin.qq.com/cgi-bin/getmessage";
		$this->appid = $appid;
		$this->appkey = $appkey;
		if(S('access_token') ==''){
        $this->access_token = $this->get_token($appid,$appkey);//公众号绑定的url的token
		S('access_token',$this->access_token);
		}
		else{
		$this->access_token = S('access_token');
		}
        $this->debug = $debug;
    }

    /**
     * 获取access_token
     *
     * @param array $param
     * @return boolean
     */
    public function get_token()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token';
        $post = 'grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appkey;
        $html = $this->_html($url, $post);
        // 判断是不是登录成功
        $json = json_decode($html,true);
        //获取 token
        if(array_key_exists("access_token",$json)){
		S('access_token',$json['access_token']);
		$this->access_token = $json['access_token'];
        return $json['access_token'];
		}
		else
		{
		S('access_token','');
		return false;
		}
    }
	    /**
     * 验证access_token超时重新获取token
     *
     * @param array $param
     * @return boolean
     */
	public function check_token($str){
	$json_arr = json_decode($str,true);
	if(isset($json_arr['errcode']) && $json_arr['errcode'] ==42001){
	$this->get_token();
	}	
	}

    /**
     * 获取最新5天关注用户发过来的消息，消息id，用户fakeid，昵称，消息内容
     *
     * 返回结构:id:msgId; fakeId; nickName; content;
     *
     * @return array
     */
    public function newmesg()
    {
        $url = 'https://mp.weixin.qq.com/cgi-bin/getmessage?t=wxm-message&token='.$this->access_token.'&lang=zh_CN&count=50&rad='.rand(10000, 99999);
        $stream = $this->_html($url);

        preg_match('/< type="json" id="json-msgList">(.*?)<\/>/is', $stream, $match);
        $json = json_decode($match[1], true);
        $returns = array();
        foreach ( $json as $val){
                if ( $val['starred'] == '0') {
                        $returns[] = $val;
                }
        }
        return $returns;
    }

    /**
     * 设置标记
     *
     * @param integer $msgId 消息标记
     * @return boolean
     */
    public function start($msgId)
    {
            $url = 'https://mp.weixin.qq.com/cgi-bin/setstarmessage?t=ajax-setstarmessage&rad='.rand(10000, 99999);
            $post = 'msgid='.$msgId.'&value=1&token='.$this->access_token.'&ajax=1';
            $stream = $this->_html($url, $post);

            // 是不是设置成功
            $html = preg_replace("/^.*\{/is", "{", $stream);
            $json = json_decode($html, true);

            return (boolean)$json['msg'] == 'sys ok';
    }

    /**
     * 发送消息
     *
     * 结构 $param = array(fakeId, content, msgId);
     * @param array $param
     * @return boolean
     */
   public function sendmesg($param)
 {
		$url  = 'https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response';
		$post = 'error=false&tofakeid='.$param['fakeId'].'&type=1&content='.$param['content'].'&quickreplyid='.$param['msgId'].'&token='.$this->access_token.'&ajax=1';

		$stream = $this->_html($url, $post);
		$this->start($param['msgId']);

		// 是不是设置成功
		$html = preg_replace("/^.*\{/is", "{", $stream);
		$json = json_decode($html, true);
		return (boolean)$json['msg'] == 'ok';
}

    /**
    * 主动发消息结构
    *  $param = array(fakeId, content);
    *  @param array $param
    *  @return [type] [description]
    */
    public function send($param)
    {
        $url  = 'https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response&lang=zh_CN';
        //$post = 'ajax=1&appmsgid='.$param['msgid'].'&error=false&fid='.$param['msgid'].'&tofakeid='.$param['fakeId'].'&token='.$this->access_token.'&type=10';
        $post = 'ajax=1&content='.$param['content'].'&error=false&tofakeid='.$param['fakeId'].'&token='.$this->access_token.'&type=1';
        $stream = $this->_html($url, $post);
        // 是不是设置成功
        $html = preg_replace("/^.*\{/is", "{", $stream);
        $json = json_decode($html, true);
        return (boolean)$json['msg'] == 'ok';
    }
     /**
    * 批量发送(可能需要设置超时)
    * $param = array(fakeIds, content);
    * @param array $param
    * @return [type] [description]
    */
    public function batSend($param)
    {   $url  = 'https://mp.weixin.qq.com/cgi-bin/masssend?t=ajax-response';
        $post = 'ajax=1&city=&content='.$param['content'].'&country=&error=false&groupid='.$param['groupid'].'&needcomment=0&province=&sex=0&token='.$this->access_token.'&type=1';
        $stream = $this->_html($url, $post);
        // 是不是设置成功
        $html = preg_replace("/^.*\{/is", "{", $stream);
        $json = json_decode($html, true);
        return (boolean)$json['msg'] == 'ok';
    }
    /*
     * 新建图文消息
     */
    public function setNews($param, $post_data)
    {
        $url = 'https://mp.weixin.qq.com/cgi-bin/sysnotify?lang=zh_CN&f=json&begin=0&count=5';
        $post = 'ajax=1&token='.$this->access_token.'';
        $stream = $this->_html($url, $post);
        //上传图片
        $url = 'https://mp.weixin.qq.com/cgi-bin/uploadmaterial?cgi=uploadmaterial&type='.$param['type'].'&token='.$this->access_token.'&t=iframe-uploadfile&lang=zh_CN&formId=1';
        $stream = $this->_uploadFile($url, $post_data);
        echo '</pre>';
        print_r($stream);
        echo '</pre>';
        exit;
    }

    /**
     * 获得用户发过来的消息（消息内容和消息类型）
     */
    public function getMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if ($this->debug) {
            $this->write_log($postStr);
        }
        if (!empty($postStr)) {
            $this->msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->msgtype = strtolower(trim($this->msg['MsgType']));//获取用户信息的类型
			$this->eventkey = strtolower(trim($this->msg['EventKey']));//获取key值
			$this->event = strtolower(trim($this->msg['Event']));//关注时间比较特别
        }
    }
    /**
     * 回复文本消息
     * @param string $text
     * @return string
     */
    public function makeText($text='')
    {
        $createtime = time();
        $funcflag = $this->setFlag ? 1 : 0;
        $textTpl = "<xml>
            <ToUserName><![CDATA[{$this->msg['FromUserName']}]]></ToUserName>
            <FromUserName><![CDATA[{$this->msg['ToUserName']}]]></FromUserName>
            <CreateTime>{$createtime}</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>%s</FuncFlag>
            </xml>";
        return sprintf($textTpl,$text,$funcflag);
    }
     /**
     * 回复图文消息
     * @param array $newsData
     * @return string
     */
     public function makeNews($newsData=array())
    {
        $createtime = time();
        $funcflag = $this->setFlag ? 1 : 0;
        $newTplHeader = "<xml>
            <ToUserName><![CDATA[{$this->msg['FromUserName']}]]></ToUserName>
            <FromUserName><![CDATA[{$this->msg['ToUserName']}]]></FromUserName>
            <CreateTime>{$createtime}</CreateTime>
            <MsgType><![CDATA[news]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <ArticleCount>%s</ArticleCount><Articles>";
        $newTplItem = "<item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
            </item>";
        $newTplFoot = "</Articles>
            <FuncFlag>%s</FuncFlag>
            </xml>";
        $content = '';
        $itemsCount = count($newsData['items']);
        $itemsCount = $itemsCount < 10 ? $itemsCount : 10;//微信公众平台图文回复的消息一次最多10条
        if ($itemsCount) {
            foreach ($newsData['items'] as $key => $item) {
                $content .= sprintf($newTplItem,$item['title'],$item['description'],$item['picUrl'],$item['url']);//微信的信息数据

            }
        }
        $header = sprintf($newTplHeader,$newsData['content'],$itemsCount);
        $footer = sprintf($newTplFoot,$funcflag);
        return $header . $content . $footer;
    }
	/**
     * 回复音乐消息
     * @param array $newsData
     * @return string
     */
    public function makeMusic($newsData=array())
    {
        $createtime = time();
        $funcflag = $this->setFlag ? 1 : 0;
        $textTpl = "<xml>
            <ToUserName><![CDATA[{$this->msg['FromUserName']}]]></ToUserName>
            <FromUserName><![CDATA[{$this->msg['ToUserName']}]]></FromUserName>
            <CreateTime>{$createtime}</CreateTime>
            <MsgType><![CDATA[music]]></MsgType>
            <Music>
			<Title><![CDATA[{$newsData['title']}]]></Title>
            <Description><![CDATA[{$newsData['description']}]]></Description>
            <MusicUrl><![CDATA[{$newsData['MusicUrl']}]]></MusicUrl>
            <HQMusicUrl><![CDATA[{$newsData['HQMusicUrl']}]]></HQMusicUrl>
			</Music>
			<FuncFlag>%s</FuncFlag>
			</xml>";
        return sprintf($textTpl,'',$funcflag);
    }
	/**
       * 得到制定分组的用户列表
       * @param number $groupid
	     @param number $pagesize，每页人数
		 @param number $pageidx，起始位置
       * @return Ambigous <boolean, string, mixed>
       */
      public function getfriendlist($groupid=0,$pagesize=500,$pageidx=0)
      {
         $url = 'https://mp.weixin.qq.com/cgi-bin/contactmanagepage?token='.$this->access_token.'&t=wxm-friend&lang=zh_CN&pagesize='.$pagesize.'&pageidx='.$pageidx.'&groupid='.$groupid;
         $referer = "https://mp.weixin.qq.com/";
         $response = $this->_html($url, $referer);
         if (preg_match('%< id="json-friendList" type="json/text">([\s\S]*?)</>%', $response, $match))         {
             $tmp = json_decode($match[1], true);
         }
         return $tmp;
     }

     /**
     * 返回给用户信息
     *
     */
   public function reply($data)
   {
       if ($this->debug) {
            $this->write_log($data);
       }
       echo $data;
   }
   /**
    *
    * @param type $log
    */
   public function write_log($log)
   {
       //这里是你记录调试信息的地方请自行完善以便中间调试
	   
	   error_log($log."\r\n", 3, "log.txt");
   }
    /**
     * 获取Stream
     *
     * @param string $url
     * @param string $post
     * @return mixed
     */
  private function _html($url, $post = FALSE)
    {
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    //curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    curl_setopt($curl, CURLOPT_REFERER,'https://mp.weixin.qq.com');// 设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 300); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $_str = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
       echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $_str;
}
	
     private function _uploadFile($url, $post = array())
    {
        ob_start();
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        if (is_array($post) && count($post)>0){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($ch, CURLOPT_COOKIE, $this->_cookie);
        curl_exec($ch);
        curl_close($ch);
        $_str = ob_get_contents();
        $_str = str_replace("script", "", $_str);
        ob_end_clean();
        return $_str;
    }
	//创建自定义菜单
	function create_menu($xjson){
	$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token;
	$respose_data = $this->_html($url, $xjson);
	//echo $respose_data ;
	$this->check_token($respose_data);
	return $respose_data;
	}
	
	private function checkSignature()
	{
        // you must define TOKEN by yourself        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = $this->url_token;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
	//首次用微信验证真假
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
	
}