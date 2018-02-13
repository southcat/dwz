<?php
/*!
@name:检查域名状态
@description:检查域名报毒状态
@author:墨渊 
@version:2.0
@time:2017-10-22
@copyright:优启梦&墨渊
*/
include './includes/common.php';
$poison=0;
$msg = null;
if ($_GET['key'] == $mail['key']) {
	$logs = array();
	//查域名状态
	$hostrow = $DB->query("select * from `frame_host` where type=1");
	foreach($hostrow as $key => $value){
		$url = $value['domain'];
		$id = $value['id'];
		$ret =curl_get('http://cgi.urlsec.qq.com/index.php?m=check&a=check&callback=url_query&url='.$url);
		$arr=json_decode(substr($ret,10,-1));
		$ret = $arr->data->results->whitetype;
		//1未知 2报毒 3绿标 4腾讯域名
		if ($ret == 2){
			$poison =1;
			$myrow = $DB->get_row("delete from `frame_host` where Id='$id'");
			$msg = $msg.'delete red address:'.$url.'<br />';
		}else{
			$msg = $msg.'no red address:'.$url.'<br />';
		}
	}
	if ($poison==1) {
		echo $msg;
		send_mail($mail['addressee'],'Uomg监控日志',$msg);
	}else{
		$result=array('code'=>0,'msg'=>'No Red Url','result'=>10007);
		echo json_encode($result);
	}

}else{
	$result=array('code'=>0,'msg'=>'Monitor code error','result'=>10008);
	echo json_encode($result);
}
$DB->close();
//删除无用变量
unset($hostrow,$myrow,$arr,$result,$msg,$ret);
