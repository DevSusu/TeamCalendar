<?php
session_start();
$val=0;
if(isset($_SESSION['pwchange'])) $val=$_SESSION['pwchange'];
else $val=-1;
unset($_SESSION['pwchange']);
echo json_encode(array('value'=>$val));
?>
