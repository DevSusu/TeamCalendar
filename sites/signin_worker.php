<?php
require("../lib/lib_teamcalendar.php");
if(islogin()) header("Location: ../index.php");
$pdo = pdoconnect();
$stmt=$pdo->prepare("INSERT IGNORE INTO `user`(`email`, `pw`, `name`) VALUES (:email,:tokenid,:name) SELECT 'value' FROM DUAL");
$stmt->bindParam(':email',$_REQUEST['Email']);
$stmt->bindParam(':name',$_REQUEST['name']);
$stmt->bindParam(':tokenid',$_REQUEST['id_token']);
$stmt->execute();
?>
