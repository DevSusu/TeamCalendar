<?php
require("../../../lib/lib_teamcalendar.php");
$pdo=pdoconnect();
$stmt=$pdo->prepare("UPDATE `request` SET `state`=2 WHERE requestid=:requestid");
$stmt->bindParam(':requestid',$_REQUEST['requestid']);
$stmt->execute();
?>
