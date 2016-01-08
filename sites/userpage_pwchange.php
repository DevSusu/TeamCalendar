<?php
require("../lib/lib_teamcalendar.php");
if(!islogin()) header("Location: ../index.php");
$pdo = pdoconnect();
$stmt = $pdo->prepare("SELECT `pw` FROM `user` WHERE userid=:id");
$stmt->bindParam(':id', $_SESSION['userid']);
$stmt->execute();
$data=$stmt->fetch();
if($_REQUEST['pwnow']==$data['pw'])
{
	if($_REQUEST['pwnew']==$_REQUEST['pwchk'])
	{
		$_SESSION['pwchange']=0;
		$stmt=$pdo->prepare("UPDATE `user` set pw=:pw WHERE userid=:id");
		$stmt->bindParam(':pw',$_REQUEST['pwnew']);
		$stmt->bindParam(':id',$_SESSION['userid']);
		$stmt->execute();
	}
	else $_SESSION['pwchange']=1;
}
else $_SESSION['pwchange']=2;
?>
