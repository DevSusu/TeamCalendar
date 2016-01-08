<?php
require("../../lib/lib_teamcalendar.php");
$pdo = pdoconnect();
$stmt=$pdo->prepare("SELECT `userid` FROM `board` WHERE boardid=:boardid");
$stmt->bindParam(':boardid',$_REQUEST['tar_boardid']);
$stmt->execute();
$data=$stmt->fetch(PDO::FETCH_ASSOC);
$chk=false;
if($data['userid']==$_SESSION['userid'] || getlevel($_REQUEST['gid'])==1) $chk=true;
if($chk)
{
	$stmt=$pdo->prepare("DELETE FROM `board` WHERE `boardid` = :boardid");
	$stmt->bindParam(':boardid',$_REQUEST['tar_boardid']);
	$stmt->execute();
	$_SESSION['delmemo']=1;
}
else $_SESSION['wrongcont']=1;

?>
