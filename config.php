<?php

/*$conn = mysql_connect("localhost","root","");
if (!$conn)
  {
  die('无法连接数据库: ' . mysql_error());
  }
 */
 
 //__________________________________pdo_______________
$dsn = "mysql:dbname=duty;host=localhost";
$db= new PDO($dsn, "root", "123456",array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
 //$db-> exec('set names utf-8');
session_start();

if(!isset($_SESSION['user'])||$_SESSION['user']!='dengqiuhua'){
    $url_current=$_SERVER["REQUEST_URI"];
    $url_login="/login.php?url=".rawurldecode($url_current);
	header("location:$url_login");
}

//----------------------------------
set_time_limit(0);//数据库延时处理

$sql="SELECT deadline FROM duty ORDER BY id DESC LIMIT 1";
$rs_limit=$db->query($sql);
$deadline_limit=$rs_limit->fetchColumn();

$deadline_limit=$deadline_limit?$deadline_limit:time();//最后一条打卡记录
$year=date('Y',$deadline_limit);
$month=intval(date('m',$deadline_limit));//月份
unset($rs_limit);