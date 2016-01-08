<?php
require("../../lib/lib_teamcalendar.php");
$pdo = pdoconnect();
$stmt=$pdo->prepare("UPDATE `todo` SET `state`=:state WHERE todoid=:todoid");
$stmt->bindParam(':state',$_REQUEST['state']);
$stmt->bindParam(':todoid',$_REQUEST['todoid']);
$stmt->execute();
 ?>
