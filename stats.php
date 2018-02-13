<?php
/*!
@name:防洪接口文件
@description:防洪接口调用文件
@author:墨渊 
@version:2.0
@time:2017-10-22
@copyright:优启梦&墨渊
*/
include './includes/common.php';

header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:GET,POST");
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header("Content-Type: text/html; charset=utf-8");
$value = NULL;
if (isset($_GET['method']))     $value = $_GET['method'];
if (isset($_POST['method']))    $value = $_POST['method'];

if ($value == 'index_num') {
	$urls=$DB->count("SELECT count(*) FROM frame_host WHERE 1"); //获取域名数量
	$logs=$DB->count("SELECT count(*) from frame_report WHERE 1"); //获取生成数量

	$result=array(
		'UrlUseNum'=>$urls,
		'ApiUseNum'=>$logs
	);
	print_r(json_encode($result));
}elseif ($value == 'tj'){
	$longurl=LongUrl('http://t.cn/'.$_GET['uid']);
	$url_arr = parse_url($longurl);

	$domain = $url_arr['host'];
	if ($domain == 't.cn') exit('401');
	$date = date("Y-m-d");
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$remoteip=real_ip();
	$DB->query("insert into `frame_log` (`domain`,`click_time`,`user_agent`,`ip_address`) values ('".$domain."','".$date."','".$user_agent."','".$remoteip."')");
}

function sinalong($url) {
	$appkey = '31641035';
	$url='https://api.weibo.com/2/short_url/expand.json?source='.$appkey.'&url_short='.urlencode($url);
	$result = curl_get($url);
	$arr = json_decode($result, true);
	return isset($arr['urls'][0]['url_long'])?$arr['urls'][0]['url_long']:false;
}

function LongUrl($url){
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // 不需要页面内容
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    // 不直接输出
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 返回最后的Location
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_exec($ch);
    $info = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
	return $info;
}