<?php

include './includes/common.php';
include './includes/phpqrcode.php';

header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:GET,POST");
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header("Content-Type: image/png; charset=utf-8");


$value = NULL;
if (isset($_GET['url']))     $value = $_GET['url'];
if (isset($_POST['url']))    $value = $_POST['url'];

if(!empty($value)){
	if(strpos($value,'http')===false){
		$url = 'http://'.daddslashes($value);
	}else{
		$url = daddslashes($value);
	}
}else{
    exit('{"msg":"URL不能为空"}');
}


// 纠错级别：L、M、Q、H
$level = 'L';
// 点的大小：1到10,用于手机端4就可以了
$size = 6;
QRcode::png($value, false, $level, $size);