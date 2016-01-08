<?php
require("../../../lib/lib_teamcalendar.php");
$pdo=pdoconnect();
$stmt=$pdo->prepare("SELECT `userid`,`groupid` FROM `request` WHERE requestid=:requestid");
$stmt->bindParam(':requestid',$_REQUEST['requestid']);
$stmt->execute();
$data=$stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt=$pdo->prepare("INSERT INTO `joined` (`userid`, `groupid`, `level`) VALUES (:userid, :groupid, 0)");
$stmt->bindParam(':userid',$data[0]['userid']);
$stmt->bindParam(':groupid',$data[0]['groupid']);
$stmt->execute();
$stmt=$pdo->prepare("UPDATE `request` SET `state`=1 WHERE requestid=:requestid");
$stmt->bindParam(':requestid',$_POST['requestid']);
$stmt->execute();
?>
