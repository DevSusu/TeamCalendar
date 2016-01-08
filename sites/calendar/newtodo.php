<?php
require("../../lib/lib_teamcalendar.php");
if(!islogin()) header("Location: ../../index.php");
$pdo=pdoconnect();
$stmt=$pdo->prepare("INSERT INTO `todo` (`context`, `deadline`,`userid`) VALUES (:context,:deadline,:userid)");
$context=htmlspecialchars($_REQUEST['context']);
$stmt->bindParam(':context',$context);
$tmp=$_REQUEST['deadline_date'].' '.$_REQUEST['deadline_time'];
$deadline=htmlspecialchars($tmp);
$stmt->bindParam(':deadline',$deadline);
$stmt->bindParam(':userid',$_SESSION['userid']);
$stmt->execute();
 ?>
