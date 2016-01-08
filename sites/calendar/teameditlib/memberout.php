<?php
require("../../../lib/lib_teamcalendar.php");
$pdo=pdoconnect();
$stmt=$pdo->prepare("DELETE FROM `joined` WHERE userid=:userid AND groupid=:groupid");
$stmt->bindParam(':userid',$_REQUEST['userid']);
$stmt->bindParam(':groupid',$_REQUEST['gid']);
$stmt->execute();
?>
