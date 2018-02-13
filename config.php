<?php
/*数据库配置*/
$dbconfig=array(
	'host' => 'localhost', //数据库服务器
	'port' => 3306, //数据库端口
	'user' => 'root', //数据库用户名
	'pwd' => 'zhc010321', //数据库密码
	'dbname' => 'url' //数据库名
);
/*网站配置*/
$conf=array(
	'admin_user' => 'admin', //管理员用户名
	'admin_pwd' => 'admin' //管理员密码
);
/*邮箱配置*/
$mail=array(
	'smtp' => 'smtp.aicey.net', //smtp地址
	'port' => 25, //端口
	'name' => 'admin@aicey.net', //邮箱帐号
	'pass' => '', //邮箱密码
	'key' => 'southcat', //监控密钥
	'addressee' => 'ac@aicey.net' //接收邮箱
);
?>