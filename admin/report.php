<?php
$mod='blank';
include("../includes/common.php");
$title='生成记录';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
  <nav class="navbar navbar-fixed-top navbar-default">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">导航按钮</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="./">后台管理</a>
      </div><!-- /.navbar-header -->
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="nav navbar-nav navbar-right">
          <li>
            <a href="./index.php"><span class="glyphicon glyphicon-user"></span> 黑白名单</a>
          </li>
          <li class="active">
            <a href="./report.php"><span class="glyphicon glyphicon-user"></span> 生成记录</a>
          </li>
          <li class="">
            <a href="./host.php"><span class="glyphicon glyphicon-user"></span> 域名管理</a>
          </li>
          <li><a href="./login.php?logout"><span class="glyphicon glyphicon-log-out"></span> 退出登陆</a></li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container -->
  </nav><!-- /.navbar -->
  <div class="container" style="padding-top:70px;">
    <div class="col-xs-12 col-sm-10 center-block" style="float: none;">
<?php
$my=isset($_GET['my'])?$_GET['my']:null;


if($my=='del'){
$id=intval($_GET['id']);
$sql=$DB->query("DELETE FROM frame_report WHERE id='$id'");
if($sql){$res='删除成功！';}
else{$res='删除失败！';}
exit("<script language='javascript'>alert('{$res}');history.go(-1);</script>");
}

if($my=='del2'){
$checkbox=$_POST['checkbox'];
$i=0;
foreach($checkbox as $id){
	$DB->query("DELETE FROM frame_report WHERE id='$id'");
	$i++;
}
exit("<script language='javascript'>alert('成功删除{$i}条记录');history.go(-1);</script>");
}

elseif($my=='qk'){//清空数据
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">清空数据</h3></div>
<div class="panel-body box">
您确认要清空所有数据吗？清空后无法恢复！<br><a href="./report.php?my=qk2">确认</a> | <a href="javascript:history.back();">返回</a></div></div>';
}
elseif($my=='qk2'){//清空数据结果
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">清空数据</h3></div>
<div class="panel-body box">';
if($DB->query("TRUNCATE TABLE frame_report")==true){
echo '<div class="box">清空成功.</div>';
}else{
echo'<div class="box">清空失败.</div>';
}
echo '<hr/><a href="./report.php">>>返回记录列表</a></div></div>';
}
else
{

if(isset($_GET['kw'])) {
	if($_GET['type']==1) {
		$sql=" `reason`='{$_GET['kw']}'";
		$numrows=$DB->count("SELECT count(*) from frame_report WHERE{$sql}");
		$con='包含 '.$_GET['kw'].' 的共有 <b>'.$numrows.'</b> 个记录';
	}elseif($_GET['type']==2) {
		$sql=" `email`='{$_GET['kw']}'";
		$numrows=$DB->count("SELECT count(*) from frame_report WHERE{$sql}");
		$con='包含 '.$_GET['kw'].' 的共有 <b>'.$numrows.'</b> 个记录';
	}elseif($_GET['type']==3) {
		$sql=" `url` LIKE '%{$_GET['kw']}%'";
		$numrows=$DB->count("SELECT count(*) from frame_report WHERE{$sql}");
		$con='包含 '.$_GET['kw'].' 的共有 <b>'.$numrows.'</b> 个记录';
	}
}else{
	$numrows=$DB->count("SELECT count(*) from frame_report WHERE 1");
	$sql=" 1";
	$con='系统共有 <b>'.$numrows.'</b> 条生成记录 [<a href="./report.php?my=qk">清空所有</a>]';
}
echo $con;
?>
      <div class="table-responsive">
	  <form name="form1" method="post" action="report.php?my=del2">
        <table class="table table-striped">
          <thead><tr><th>网址</th><th>生成IP</th><th>时间</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=30;
$pages=intval($numrows/$pagesize);
if ($numrows%$pagesize)
{
 $pages++;
 }
if (isset($_GET['page'])){
$page=intval($_GET['page']);
}
else{
$page=1;
}
$offset=$pagesize*($page - 1);

$rs=$DB->query("SELECT * FROM frame_report WHERE{$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
echo '<tr><td><input type="checkbox" name="checkbox[]" value="'.$res['id'].'"> <a href="'.htmlspecialchars($res['url']).'" rel="noreferrer" target="_blank">'.htmlspecialchars($res['url']).'</a></td><td><a href="mailto:'.htmlspecialchars($res['email']).'">'.htmlspecialchars($res['ip']).'</td><td>'.$res['date'].'</td><td><a href="./index.php?my=add&url='.urlencode(htmlspecialchars($res['url'])).'" class="btn btn-xs btn-warning">添加黑名单</a>&nbsp;<a href="./report.php?my=del&id='.$res['id'].'" class="btn btn-xs btn-danger" onclick="return confirm(\'你确实要删除此记录吗？\');">删除</a></td></tr>';
}
?>
          </tbody>
        </table>
<input type="submit" name="Submit" value="删除选中">
</form>
      </div>
<?php
echo'<ul class="pagination">';
$first=1;
$prev=$page-1;
$next=$page+1;
$last=$pages;
if ($page>1)
{
echo '<li><a href="report.php?page='.$first.$link.'">首页</a></li>';
echo '<li><a href="report.php?page='.$prev.$link.'">&laquo;</a></li>';
} else {
echo '<li class="disabled"><a>首页</a></li>';
echo '<li class="disabled"><a>&laquo;</a></li>';
}
for ($i=1;$i<$page;$i++)
echo '<li><a href="report.php?page='.$i.$link.'">'.$i .'</a></li>';
echo '<li class="disabled"><a>'.$page.'</a></li>';
for ($i=$page+1;$i<=$pages;$i++)
echo '<li><a href="report.php?page='.$i.$link.'">'.$i .'</a></li>';
echo '';
if ($page<$pages)
{
echo '<li><a href="report.php?page='.$next.$link.'">&raquo;</a></li>';
echo '<li><a href="report.php?page='.$last.$link.'">尾页</a></li>';
} else {
echo '<li class="disabled"><a>&raquo;</a></li>';
echo '<li class="disabled"><a>尾页</a></li>';
}
echo'</ul>';
#分页
}
?>
    </div>
  </div>