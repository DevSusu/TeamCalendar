<?php
    require("../../lib/lib_teamcalendar.php");
    if(!islogin()) header('Location: ../../index.php');
?>
<img id="loadimg" src="calendar/ajax-loader.gif" alt="Loading.." class="hide"/>
<h1>팀 검색</h1>
<form class="form-inline">
  <input type="text" id="searchtext" class="form-control" name="S" placeholder="검색할 팀 이름을 입력하세요." width="50%" required>
  <button type="button" onclick="worker_search();" class="btn btn-info btn-xs">검색</button>
</form><br>
<table class="table table-bordered table-hover" width="65%">
	<tr>
		<th>팀 이름</th>
		<th>팀장</th>
		<th></th>
	</tr>
	<?php
  $pdo=pdoconnect();
	$stmt=$pdo->prepare("SELECT * FROM `group` WHERE `name` LIKE :str");
	$str='%'.$_GET['S'].'%';
	$stmt->bindParam(':str',$str);
	$stmt->execute();
	$data=$stmt->fetchAll(PDO::FETCH_ASSOC);
	for($i=0;$i<count($data);$i++)
	{
		$stmt=$pdo->prepare("SELECT `user`.`name` FROM `user` LEFT JOIN `joined` ON `user`.`userid`=`joined`.`userid` WHERE `joined`.`groupid`=:groupid AND `joined`.`level`=1");
		$stmt->bindParam(':groupid',$data[$i]['groupid']);
		$stmt->execute();
		$tmp=$stmt->fetchAll(PDO::FETCH_ASSOC);
		$pstr="<tr><td>{$data[$i]['name']}</td><td>{$tmp[0]['name']}</td><td><button onclick=\"worker_request({$data[$i]['groupid']});\" class=\"btn btn-info\">가입 신청</button></td></tr>";
		echo $pstr.'<br>';
	}
	?>
</table>
<script type="text/javascript">
  function worker_request(groupid)
  {
    $('#loadimg').toggleClass("hide show");
    $.ajax({
      url:'calendar/request.php',
      type:'post',
      dataType:'json',
      data:{'groupid':groupid},
      complete:function()
      {
        $('#main').load('calendar/teamcalendaredit.php');
      }
    })
  }
  function worker_search()
  {
    $('#loadimg').toggleClass("hide show");
    $('#main').load('calendar/searchgroup.php?S='+$('#searchtext').val());
  }
</script>
