<?php
//error_reporting(0);
define('IN_CRONLITE', true);
define('VERSION', '2510');
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');
define('SYS_KEY', 'Jump');
define('ALPHABET', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

date_default_timezone_set("PRC");
$date = date("Y-m-d H:i:s");
session_start();

if(is_file(SYSTEM_ROOT.'360safe/360webscan.php')){//360网站卫士
    require_once(SYSTEM_ROOT.'360safe/360webscan.php');
}

require ROOT.'config.php';

//连接数据库
include_once(SYSTEM_ROOT."db.class.php");
$DB=new DB($dbconfig['host'],$dbconfig['user'],$dbconfig['pwd'],$dbconfig['dbname'],$dbconfig['port']);


//加载文件
require (SYSTEM_ROOT."function.php");
require (SYSTEM_ROOT."txprotect.php");
require (SYSTEM_ROOT."member.php");
?>