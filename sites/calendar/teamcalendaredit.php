<?php
    require("../../lib/lib_teamcalendar.php");
    if(!islogin()) header('Location: ../../index.php');
?>
<img id="loadimg" src="calendar/ajax-loader.gif" alt="Loading.." class="hide"/>
<div class="alert alert-success alert-dismissible" role="alert" style="display: none" id="ok_team">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>새로운 팀이 만들어 졌습니다!</strong> 팀원들과 일정을 공유하세요.
</div>
<div class="alert alert-danger alert-dismissible" role="alert" style="display: none" id="no_team">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>동일한 이름의 팀이 이미 있습니다!</strong> 다른 이름으로 입력해 주세요.
</div>
<div class="alert alert-success alert-dismissible" role="alert" style="display: none" id="delteam">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>팀을 삭제하였습니다.</strong>
</div>
<div class="alert alert-success alert-dismissible" role="alert" style="display: none" id="outteam">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>팀에서 탈퇴하였습니다.</strong>
</div>
<div class="alert alert-warning alert-dismissible" role="alert" style="display: none" id="requestfail">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>이미 가입중인 팀입니다.</strong>
</div>
<div class="alert alert-success alert-dismissible" role="alert" style="display: none" id="requestok">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>가입요청을 했습니다.</strong> 팀 가입을 위해 가입요청을 하였습니다.
</div>
<div class="alert alert-warning alert-dismissible" role="alert" style="display: none" id="requested">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>이미 가입요청을 했습니다.</strong>
</div>

<h1>팀 캘린더</h1>
<form class="form-inline">
  <input type="text" id="searchtext" class="form-control" placeholder="검색할 팀 이름을 입력하세요." width="50%" required>
  <button type="button" onclick="worker_search();" class="btn btn-info btn-xs">검색</button>
</form><br>
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#newteam">팀 캘린더 만들기</button>
<div class="modal fade" id="newteam" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">새로운 팀 캘린더</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="pw_now">팀 이름</label>
						<input type="text" class="form-control" id="newteamname" name="name" placeholder="팀 이름을 입력하세요." required>
					</div>
					<button type="button" onclick="newteam();" data-dismiss="modal" class="btn btn-primary">만들기</button>
				</form>
			</div>
			<div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
      </div>
		</div>
  </div>
</div>
<br>
<br>
<table class="table table-bordered">
	<thead>
		<tr>
      <th>켈린더 이름</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<!-- 사용자 팀 캘린더 링크 -->
		<?php
    $pdo = pdoconnect();
    $stmt=$pdo->prepare("SELECT `groupid` FROM `joined` WHERE userid=:id");
    $stmt->bindParam(':id',$_SESSION['userid']);
    $stmt->execute();
    $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
    for($i=0;$i<count($data);$i++)
    {
      $stmt=$pdo->prepare("SELECT `name` FROM `group` WHERE groupid=:id");
      $id=$data[$i]['groupid'];
      $stmt->bindParam(':id',$id);
      $stmt->execute();
      $name=$stmt->fetch(PDO::FETCH_ASSOC);
      echo "<tr><td><a href=\"#myteam{$id}\" onclick=\"openteamcalendar({$id})\" class=\"calendarname\">{$name['name']}</a></td>";
      echo "<td><button class=\"btn btn-danger\" type=\"button\" onclick=\"worker_groupdel({$id});\">삭제</button></td></tr>";
    }
		?>
	</tbody>
</table>
<script>
  function worker_groupdel(tarid)
  {
    confirm_str=prompt("팀에서 탈퇴합니다! 정말로 탈퇴하겠습니까? \"OUT\"을 입력해주세요.  - 팀장이 탈퇴할 경우 팀은 자동 해체됨니다.");
    if(confirm_str=="OUT")
    {
      $('#loadimg').toggleClass("hide show");
      $.ajax({
        url:'calendar/calendar_del.php',
        type:'post',
        dataType:'json',
        data:{'gid':tarid},
        complete:function()
        {
          $('#main').load('calendar/teamcalendaredit.php');
        }
      })
    }
  }
  function worker_search()
  {
    $('#loadimg').toggleClass("hide show");
    $('#main').load('calendar/searchgroup.php?S='+$('#searchtext').val());
  }
  function openteamcalendar(groupid)
  {
    $('#loadimg').toggleClass("hide show");
    $('#main').load('calendar/teamcalendar.php?id='+groupid);
  }
  function newteam()
  {
    $('#loadimg').toggleClass("hide show");
    $.ajax({
      url:'calendar/newcalendar.php',
      type:'post',
      dataType:'json',
      data:{'name':$('#newteamname').val()},
      complete:function()
      {
        $('#main').load('calendar/teamcalendaredit.php');
      }
    })
  }
  var bool_team=<?php if(isset($_SESSION['createteam'])) echo $_SESSION['createteam'];
  else echo "-1";
  $_SESSION['createteam']=-1;?>;
  if(bool_team==1) $('#no_team').css('display','block');
  if(bool_team==0) $('#ok_team').css('display','block');
  var delteam=<?php
  if(isset($_SESSION['delgroup']))
  {
    echo $_SESSION['delgroup'];
    unset($_SESSION['delgroup']);
  }else echo 0;?>;
  if(delteam==1) $('#delteam').css('display','block');
  if(delteam==2) $('#outteam').css('display','block');
  var request=<?php
  if(isset($_SESSION['requestgroup']))
  {
    echo $_SESSION['requestgroup'];
    unset($_SESSION['requestgroup']);
  }else echo -1;?>;
  if(request==0) $('#requestok').css('display','block');
  if(request==1) $('#requestfail').css('display','block');
  if(request==2) $('#requested').css('display','block');
</script>
