<?php
require("../lib/lib_teamcalendar.php");
if(!islogin()) header("Location: ../index.php");
$pdo = pdoconnect();
$stmt=$pdo->prepare("DELETE FROM `request` WHERE `requestid` = :requestid");
$stmt->bindParam(':requestid',$_REQUEST['requestid']);
$stmt->execute();

?>
