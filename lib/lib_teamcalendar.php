<?php
session_start();
function footer_out()
{
	echo "Made By Antonio Min, 2015, gvvvv1123@gmail.com";
}
function pdoconnect()
{
	$dsn='mysql:host=localhost;dbname=teamcalendar;charset=utf8';
	$user='root';
	$password='min3956';
	$pdo = new PDO($dsn,$user,$password);
	$stmt=$pdo->prepare("set session character_set_connection=utf8");
	$stmt->execute();
	$stmt=$pdo->prepare("set session character_set_results=utf8");
	$stmt->execute();
	$stmt=$pdo->prepare("set session character_set_client=utf8");
	$stmt->execute();
	return $pdo;
}
function islogin()
{
	if(isset($_SESSION['userid'])) return 1;
	else return 0;
}
function isjoined($groupid)
{
	$pdo=pdoconnect();
	$stmt=$pdo->prepare("SELECT * FROM `joined` WHERE userid=:userid AND groupid=:groupid");
	$stmt->bindParam(':userid',$_SESSION['userid']);
	$stmt->bindParam(':groupid',$groupid);
	$stmt->execute();
	$data=$stmt->fetch();
	if(isset($data['level'])) return 1;
	else return 0;
}
function getlevel($groupid)
{
	$pdo=pdoconnect();
	$stmt=$pdo->prepare("SELECT `level` FROM `joined` WHERE userid=:userid AND groupid=:groupid");
	$stmt->bindParam(':userid',$_SESSION['userid']);
	$stmt->bindParam(':groupid',$groupid);
	$stmt->execute();
	$data=$stmt->fetch();
	return $data['level'];
}

?>
