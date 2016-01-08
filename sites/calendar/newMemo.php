<?php
require("../../lib/lib_teamcalendar.php");
if(!islogin()) header("Location: ../../index.php");
$pdo = pdoconnect();
$cm=$pdo->prepare("INSERT INTO `board` (`userid`, `groupid`, `memo`) VALUES (:userid, :groupid, :memo)");
$cm->bindParam(':userid',$_SESSION['userid']);
$str=htmlspecialchars($_REQUEST['memo']);
$cm->bindParam(':memo',$str);
$cm->bindParam(':groupid',$_REQUEST['groupid']);
$cm->execute();
$_SESSION['okmemo']=1;
?>
