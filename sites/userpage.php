<img id="loadimg" src="calendar/ajax-loader.gif" alt="Loading.." class="hide"/>
<div class="alert alert-success alert-dismissible" role="alert" style="display: none" id="pwsuccess">
  <strong>변경 완료!</strong> 비밀번호를 성공적으로 변경하였습니다.
</div>
<div class="alert alert-danger alert-dismissible" role="alert" style="display: none" id="nopwnew">
  <strong>변경 실패!</strong> 새 비밀번호를 똑같이 써주세요.
</div>
<div class="alert alert-danger alert-dismissible" role="alert" style="display: none" id="nopwnow">
  <strong>변경 실패!</strong> 현재 비밀번호를 제대로 써주세요.
</div>

<h1>내 정보</h1>
<div>이름 : <span id="_user2"></span></div>
<div>이메일 : <span id="_email"></span></div>
<br>
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#changepw">비밀번호 변경</button>
<br>
<table class="table table-sm">
  <tr>
    <th>신청 팀 이름</th>
    <th>신청일</th>
    <th>상태</th>
  </tr>
  <?php
  require("../lib/lib_teamcalendar.php");
  $pdo=pdoconnect();
  $stmt=$pdo->prepare("SELECT * FROM `request` LEFT JOIN `group` ON `group`.`groupid`=`request`.`groupid` WHERE userid=:userid");
  $stmt->bindParam(':userid',$_SESSION['userid']);
  $stmt->execute();
  $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
  for($i=0;$i<count($data);$i++)
  {
    if($data[$i]['state']==0) $state="대기중";
    else if($data[$i]['state']==1) $state="가입 완료";
    else if($data[$i]['state']==2) $state="가입 거절";
    $str="<tr onclick=\"request_cancel({$data[$i]['requestid']});\"><td>{$data[$i]['name']}</td><td>{$data[$i]['requesteddate']}</td><td>{$state}</td></tr><br>";
    echo $str;
  }
  ?>
</table>
<!-- PassWord Change Modal -->
<div class="modal fade" id="changepw" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">비밀번호 변경</h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="pw_now">현재 비밀번호</label>
            <input type="password" class="form-control" id="pwnow" name="pwnow" placeholder="현재 비밀번호를 입력하세요." required>
          </div>
          <br>
          <div class="form-group">
            <label for="pw_new">새 비밀번호</label>
            <input type="password" class="form-control" id="pwnew" name="pwnew" placeholder="새 비밀번호를 입력하세요." required>
          </div>
          <div class="form-group">
            <label for="pw_newchk">새 비밀번호 확인</label>
            <input type="password" class="form-control" id="pwchk" name="pwchk" placeholder="새 비밀번호를 한번더 입력하세요." required>
          </div>
          <button onclick="submit_worker();" class="btn btn-primary" data-dismiss="modal">비밀번호 변경</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function alertrefresh()
  {
    $('#loadimg').toggleClass("hide show");
    $.ajax({
      url:'pwchange_alert.php',
      type:'POST',
      dataType:'json',
      success:function(data)
      {
        if(data['value']==0) $('#pwsuccess').css('display','block');
        if(data['value']==1) $('#nopwnew').css('display','block');
        if(data['value']==2) $('#nopwnow').css('display','block');
      }
    })
  }
  function submit_worker()
  {
    $('#loadimg').toggleClass("hide show");
    $('#pwnow').css('display','none');
    $('#pwnew').css('display','none');
    $('#pwchk').css('display','none');
    $('#pwnow').val(CryptoJS.SHA3($('#pwnow').val()));
    $('#pwnew').val(CryptoJS.SHA3($('#pwnew').val()));
    $('#pwchk').val(CryptoJS.SHA3($('#pwchk').val()));
    $.ajax({
      url:'userpage_pwchange.php',
      type: "POST",
      dataType:'json',
      data:{'pwnow':$('#pwnow').val(),'pwnew':$('#pwnew').val(),'pwchk':$('#pwchk').val()},
      complete:function()
      {
        alertrefresh();
      }
    });
    $('#pwnow').css('display','block').val('');
    $('#pwnew').css('display','block').val('');
    $('#pwchk').css('display','block').val('');
  }
  function request_cancel(requestid)
  {
    var str=prompt("가입 요청을 취소 하시겠습니까? (가입 완료 상태라면 요청기록을 삭제합니다.) - YES를 입력하세요.");
    if(str=="YES")
    {
      $('#loadimg').toggleClass("hide show");
      $.ajax({
        url:'requestcancel.php',
        type: "POST",
        dataType:'json',
        data:{'requestid':requestid},
        complete:function()
        {
          $('#main').load('userpage.php');
        }
      })
    }
  }
	var text=<?php echo "\"".$_SESSION['username']." 님\";"; ?>
  $('#_user').html(text);
  $('#_user2').html(text);
  var Temail=<?php
  $stmt = $pdo->prepare("SELECT `email` FROM `user` WHERE userid=:id");
  $stmt->bindParam(':id', $id);
  $id = $_SESSION['userid'];
  $stmt->execute();
  $data=$stmt->fetch();
  echo "\"".$data['email']."\""; ?>;
  $('#_email').html(Temail);
</script>
