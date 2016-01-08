<?php
require("../../lib/lib_teamcalendar.php");
$pdo =pdoconnect();
$stmt=$pdo->prepare("SELECT `userid` FROM `event` WHERE eventid=:eventid");
$stmt->bindParam(':eventid',$_REQUEST['tar_eventid']);
$stmt->execute();
$data=$stmt->fetch(PDO::FETCH_ASSOC);
$chk=false;
if($data['userid']==$_SESSION['userid'] || getlevel($_REQUEST['gid'])) $chk=true;
if($chk)
{
	$stmt=$pdo->prepare("DELETE FROM `event` WHERE `eventid` = :eventid");
	$stmt->bindParam(':eventid',$_REQUEST['tar_eventid']);
	$stmt->execute();
	$_SESSION['delevent']=1;
}
else $_SESSION['wrongcont']=1;

?>
