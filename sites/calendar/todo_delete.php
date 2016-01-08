<?php
require("../../lib/lib_teamcalendar.php");
$pdo = pdoconnect();
$stmt=$pdo->prepare("DELETE FROM `todo` WHERE todoid=:todoid");
$stmt->bindParam(':todoid',$_REQUEST['todoid']);
$stmt->execute();
 ?>
