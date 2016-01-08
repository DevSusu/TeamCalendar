<?php
require("../../lib/lib_teamcalendar.php");
$datestart=$_REQUEST['sday'].$_REQUEST['stime'];
$dateend=$_REQUEST['eday'].$_REQUEST['etime'];
$pdo = pdoconnect();
$cm=$pdo->prepare("INSERT INTO `event` (`title`, `datestart`, `dateend`, `memo`, `userid`, `groupid`) VALUES (:name, :stime, :etime, :memo, :userid, :groupid)");
$str1=htmlspecialchars($_REQUEST['eventname']);
$cm->bindParam(':name',$str1);
$dates=htmlspecialchars($datestart);
$cm->bindParam(':stime',$dates);
$datee=htmlspecialchars($dateend);
$cm->bindParam(':etime',$datee);
$str2=htmlspecialchars($_REQUEST['memo']);
$cm->bindParam(':memo',$str2);
$cm->bindParam(':userid',$_SESSION['userid']);
$cm->bindParam(':groupid',$_REQUEST['gid']);
$cm->execute();
$_SESSION['okevent']=1;
?>
