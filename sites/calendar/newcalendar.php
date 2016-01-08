<?php
require("../../lib/lib_teamcalendar.php");
if(!islogin()) header("Location: ../../index.php");
$pdo =pdoconnect();
$stmt = $pdo->prepare("SELECT `name` FROM `group` WHERE name=:name");
$stmt->bindParam(':name', $_REQUEST['name']);
$stmt->execute();
$data=$stmt->fetch();
if(count($data['name'])) $_SESSION['createteam']=1;
else
{
	$stmt = $pdo->prepare("INSERT INTO `group` (`groupid`, `name`) VALUES (NULL, :name)");
	$str=htmlspecialchars($_REQUEST['name']);
	$stmt->bindParam(':name', $str);
	$stmt->execute();
	$stmt = $pdo->prepare("SELECT `groupid` FROM `group` WHERE name=:name");
	$stmt->bindParam(':name',$_REQUEST['name']);
	$stmt->execute();
	$data=$stmt->fetch();
	$stmt=$pdo->prepare("INSERT INTO `joined` (`userid`, `groupid`, `level`) VALUES (:userid, :groupid, 1)");
	$stmt->bindParam(':userid',$_SESSION['userid']);
	$stmt->bindParam(':groupid',$data['groupid']);
	$stmt->execute();
	$_SESSION['createteam']=0;
}
?>
