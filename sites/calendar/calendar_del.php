<?php
require("../../lib/lib_teamcalendar.php");
$pdo = pdoconnect();
if(getlevel($_POST['gid'])==1)
{
	$stmt=$pdo->prepare("DELETE FROM `joined` WHERE `groupid` = :groupid");
	$stmt->bindParam(':groupid',$_POST['gid']);
	$stmt->execute();
	$stmt=$pdo->prepare("DELETE FROM `event` WHERE `groupid` = :groupid");
	$stmt->bindParam(':groupid',$_POST['gid']);
	$stmt->execute();
	$stmt=$pdo->prepare("DELETE FROM `board` WHERE `groupid` = :groupid");
	$stmt->bindParam(':groupid',$_POST['gid']);
	$stmt->execute();
	$stmt=$pdo->prepare("DELETE FROM `group` WHERE `groupid` = :groupid");
	$stmt->bindParam(':groupid',$_POST['gid']);
	$stmt->execute();
	$_SESSION['delgroup']=1;
}
else
{
	$stmt=$pdo->prepare("DELETE FROM `joined` WHERE `userid` = :userid AND `groupid` = :groupid");
	$stmt->bindParam(':userid',$_SESSION['userid']);
	$stmt->bindParam(':groupid',$_POST['gid']);
	$stmt->execute();
	$_SESSION['delgroup']=2;
}
header('Location: teamcalendaredit.php');
?>
