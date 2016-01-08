<?php
require("../lib/lib_teamcalendar.php");
if(islogin()) header("Location: ../index.php");
$pdo = pdoconnect();
$stmt = $pdo->prepare("SELECT `userid`,`pw`,`name` FROM `user` WHERE email=:email");
$stmt->bindParam(':email', $email);
$email = $_POST['email'];
$stmt->execute();
$data=$stmt->fetch();
if($data['userid']==null) echo 1;
else if($_POST['pw']==$data['pw'])
{
	echo 0;
	$_SESSION['userid']=$data['userid'];
	$_SESSION['username']=$data['name'];
}
else echo 1;
?>
