<?php
header("Content-type:text/html;charset=utf-8");
include_once('./function.php');
define('ROOT',dirname(dirname(__FILE__)));
$verMsg = 'Beta7.0';
$s_lang = 'utf-8';
$source_file = "./../../.example.env";//源配置文件
$target_file="./../../.env";  //目标配置文件。
$lock_file = '../install.lck';//锁定文件
if(file_exists($lock_file)){header('Location:../index.php');exit();}
$file_name = "./source/dami.sql";//数据文件
$step = (isset($_REQUEST['step'])&& is_numeric($_REQUEST['step']))?(int)$_REQUEST['step']:1;
$dbport = isset($_REQUEST['dbport'])?$_REQUEST['dbport']:'3306';
$dbhost = isset($_REQUEST['dbhost'])?$_REQUEST['dbhost']:'';
$dbhost = ($dbport!='3306'?$dbhost.':'.$dbport:$dbhost);
$dbuser = isset($_REQUEST['dbuser'])?$_REQUEST['dbuser']:'';
$dbpwd = isset($_REQUEST['dbpwd'])?$_REQUEST['dbpwd']:'';
$dbname = isset($_REQUEST['dbname'])?$_REQUEST['dbname']:'';

if($step==1)
{
    include('./templates/step-1.html');
    exit();
}
/*------------------------
环境测试
function _2_TestEnv()
------------------------*/
else if($step==2)
{
    $phpv = phpversion();
    $php_right = is_php()?'<font color=green>[√]</font>' : '<font color=red>[×]版本不满足要求</font>';
    $sp_os = PHP_OS;
    $sp_gd = gdversion();
    $sp_server = $_SERVER['SERVER_SOFTWARE'];
    $sp_host = (empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR']);
    $sp_name = $_SERVER['SERVER_NAME'];
    $sp_max_execution_time = ini_get('max_execution_time');
    $sp_allow_reference = (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_allow_url_fopen = (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_gd = ($sp_gd>0 ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_mysql = (function_exists('mysqli_connect') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');

    if($sp_mysql=='<font color=red>[×]Off</font>')
    $sp_mysql_err = TRUE;
    else
    $sp_mysql_err = FALSE;
    $sp_testdirs = array(
        '/',
        '/Public/*',
        '/install/*',
		'/Trade/*',
        '/../runtime/*',
        '/../config/*',
        '/../app/home/config/*',
        '/../app/admin/config/*',
        '/../.env',
    );
    include('./templates/step-2.html');
    exit();
}
/*------------------------
设置参数
function _3_WriteSeting()
------------------------*/
else if($step==3)
{
    include('./templates/step-3.html');
    exit();
}
/*------------------------
普通安装
function _4_Setup()
------------------------*/
else if($step==4)
{
    $admin_name= $_POST['admin_name'];
    $admin_pwd = $_POST['admin_pwd'];
	$vip_name= $_POST['vip_name'];
    $vip_pwd = $_POST['vip_pwd'];
    $admin_pwd2 = $_POST['admin_pwd2'];
    if($dbhost =='' || $dbuser==''){echo "<script>alert('数据库链接信息丢失!');history.go(-1);</script>";exit();}
    if(!is_writable($target_file)){    //判断是否有可写的权限，linux操作系统要注意这一点，windows不必注意。
    echo "<script>alert('配置文件不可写，权限不够!');history.go(-1);</script>";
    exit();
    }
    if(!$conn=@mysqli_connect($dbhost,$dbuser,$dbpwd)){
    echo "<script>alert('连接数据库失败！请返回上一页检查连接参数');history.go(-1);</script>";
    exit();
    }
    if($admin_name =='' || $admin_pwd ==''){
        echo "<script>alert('管理员用户名与密码不能为空!');history.go(-1);</script>";
        exit();
    }
    if($admin_pwd != $admin_pwd2){
        echo "<script>alert('两次密码输入不一致!');history.go(-1);</script>";
        exit();
    }
if(!mysqli_select_db($conn,$dbname)){   //如果数据库不存在，我们就进行创建。
         $dbsql="CREATE DATABASE `$dbname`";
         if(!mysqli_query($conn,$dbsql)){
           echo "<script>alert('创建数据库失败，请确认是否有足够的权限！');history.go(-1);</script>";
           exit();
          }
}
mysqli_select_db($conn,$dbname);     
//修改配置文件
	            $fp = fopen($source_file,"r");
				$configStr = fread($fp,filesize($source_file));
				fclose($fp);
				$configStr = str_replace('127.0.0.1',$dbhost,$configStr);
				$configStr = str_replace('damidb',$dbname,$configStr);
				$configStr = str_replace('username',$dbuser,$configStr);
				$configStr = str_replace('dbpassword',$dbpwd,$configStr);
                if($dbport!='3306'){$configStr = str_replace('3306',$dbport,$configStr);}
				$fp = fopen($target_file,"w") or die("<script>alert('写入配置失败，请检查{$target_file}是否可写入！');history.go(-1);</script>");
				fwrite($fp,$configStr);
				fclose($fp);
//导入SQL并执行。
$get_sql_data = file_get_contents($file_name);
//去掉注释 
$content=preg_replace("/--.*\n/iU","",$get_sql_data); 
//替换前缀这里可以考虑替换哦dami_的数据库前缀 但为啦系统的知名度暂时不替换 
//$content=str_replace("dami_",TABLE_PRE,$content); 
$carr=array(); 
$iarr=array(); 
mysqli_query($conn,"SET NAMES utf8");
//提取create 
preg_match_all("/Create table .*\(.*\).*\;/iUs",$get_sql_data,$carr); 
$carr=$carr[0]; 
foreach($carr as $c) 
{ 
@mysqli_query($conn,$c); 
} 
//提取insert 
preg_match_all("/INSERT INTO .*\(.*\)\;/iUs",$get_sql_data,$iarr); 
$iarr=$iarr[0]; 
//插入数据 
foreach($iarr as $c) 
{ 
@mysqli_query($conn,$c); 
}
//插入管理员信息
$admin_pwd = md5('wk'.$admin_pwd.'cms');
$admin_sql = "INSERT INTO `dami_admin` (`id`, `username`, `password`, `lastlogintime`, `lastloginip`, `status`, `is_client`) VALUES (1, '{$admin_name}', '{$admin_pwd}', 1435742234, '127.0.0.1', 1, 0);";
@mysqli_query($conn,$admin_sql);
if($vip_name != "" && $vip_pwd != ""){
$admin_sql = "INSERT INTO `dami_vip_mess` (`vip_name`, `vip_pwd`) VALUES ('{$vip_name}', '{$vip_pwd}');";
@mysqli_query($conn,$admin_sql);
}

@mysqli_close($conn);
//清理缓存文件
clean_dir('./../../runtime/');
$fp = fopen($lock_file,"w") or die("<script>alert('写入失败，请检查目录".dirname(dirname(__FILE__))."是否可写入！');history.go(-1);</script>");
fwrite($fp,'已安装');
fclose($fp);
include('./templates/step-4.html');
exit();
}
/*------------------------
检测数据库是否有效
------------------------*/
else if($step==10)
{
    header("Pragma:no-cache\r\n");
    header("Cache-Control:no-cache\r\n");
    header("Expires:0\r\n");
	if($dbhost=='' || $dbuser==''){exit();}	
    $conn = @mysqli_connect($dbhost,$dbuser,$dbpwd);
    if($conn)
    {
		if(empty($dbname)){
			echo "<font color='green'>数据库连接成功</font>";
		}else{
			$info = mysqli_select_db($conn,$dbname)?"<font color='green'>数据库存在，系统将覆盖数据库</font>":"<font color='green'>数据库不存在,系统将自动创建</font>";
			echo $info;
		}
    }
    else
    {
        echo "<font color='red'>数据库连接失败！</font>";
    }
    @mysqli_close($conn);
    exit();
}


?>