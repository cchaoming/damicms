<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if IE 9 ]><html class="ie ie9" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>页面提示</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="/public/css/bootstrap.min.css" type=text/css rel=stylesheet>
</head>
<body>
<php>$jumpUrl = !isset($jumpUrl)?'javascript:history.back(-1)':$jumpUrl;</php>
<div class="panel panel-default" style="width:80%;margin:20% auto;">
  <div class="panel-heading">
    <h3 class="panel-title">操作信息</h3>
  </div>
  <div class="panel-body">
<present name="message" >
  <font color=red>{$message|raw}</font><br><br><a href="javascript:history.back(-1)" style="text-decoration:none;"><u>返回上一页</u></a>
  </tr>
</present>
<present name="error" >
  <font color=red>{$error|raw}</font>
</present>
<present name="closeWin" >
系统将在 <span style="color:blue;font-weight:bold" id="wait">{$waitSecond}</span> 秒后自动关闭，如果不想等待,直接点击 <a href="{$jumpUrl|raw}" id="href" >这里</a> 关闭
</present>
<notpresent name="closeWin" >
系统将在 <span style="color:blue;font-weight:bold" id="wait">{$waitSecond}</span> 秒后自动跳转,如果不想等待,直接点击 <a href="{$jumpUrl|raw}" id="href" >这里</a> 跳转
</notpresent>	
<script type="text/javascript">
(function(){
  var init_wait = {$waitSecond};
  if(init_wait >0) {
    var wait = document.getElementById('wait'), href = document.getElementById('href').href;
    var interval = setInterval(function () {
      var time = --wait.innerHTML;
      wait.innerHTML = time;
      if (time <= 0) {
        location.href = href;
        clearInterval(interval);
      }
      ;
    }, 1000);
  }
})();
</script>
  </div>
</div>
</body>
</html>
