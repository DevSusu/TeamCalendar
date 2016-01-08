<?php
require("../../lib/lib_teamcalendar.php");
$pdo = pdoconnect();
if(isjoined($_REQUEST['groupid'])) $_SESSION['requestgroup']=1;
else
{
	$stmt=$pdo->prepare("SELECT `requestid` FROM `request` WHERE userid=:userid AND groupid=:groupid");
	$stmt->bindParam(':userid',$_SESSION['userid']);
	$stmt->bindParam(':groupid',$_REQUEST['groupid']);
	$stmt->execute();
	$data=$stmt->fetch();
	if(isset($data['requestid'])) $_SESSION['requestgroup']=2;
	else
	{
		$stmt=$pdo->prepare("INSERT INTO `request`(`userid`, `groupid`) VALUES (:userid,:groupid)");
		$stmt->bindParam(':userid',$_SESSION['userid']);
		$stmt->bindParam(':groupid',$_REQUEST['groupid']);
		$stmt->execute();
		$_SESSION['requestgroup']=0;
	}
}
?>
