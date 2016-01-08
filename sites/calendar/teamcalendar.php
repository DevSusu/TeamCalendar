<?php
    require("../../lib/lib_teamcalendar.php");
    if(!islogin() || !isset($_GET['id']) || !isjoined($_GET['id'])) header('Location: ../../index.php');
    $pdo = pdoconnect();
?>
<script type="text/javascript">
  var date= new Date();
  var Y=date.getFullYear(),M=date.getMonth() + 1;
</script>
<img id="loadimg" src="calendar/ajax-loader.gif" alt="Loading.." class="hide"/>
<div class="alert alert-success alert-dismissible" role="alert" style="display: none" id="okevent">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>등록 완료!</strong> 새로운 일정을 등록하였습니다.
</div>
<div class="alert alert-success alert-dismissible" role="alert" style="display: none" id="delevent">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>삭제 완료!</strong> 일정을 삭제하였습니다.
</div>
<div class="alert alert-success alert-dismissible" role="alert" style="display: none" id="okmemo">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>등록 완료!</strong> 새로운 메모를 등록하였습니다.
</div>
<div class="alert alert-success alert-dismissible" role="alert" style="display: none" id="delmemo">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>삭제 완료!</strong> 메모를 삭제하였습니다.
</div>
<div class="alert alert-danger alert-dismissible" role="alert" style="display: none" id="wrongcont">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>삭제 실패!</strong> 삭제 권환이 없습니다.
</div>
<button onclick="checker();" class="btn btn-info btn-xs">팀 관리</button>
<!-- Message Alert -->
<h1 id="teamname"></h1>
<br>
<div class="container">
  <span id="year"></span>
  <button onclick="showCalendar(Y+1,M)">+</button>
  <button onclick="showCalendar(Y-1,M)">-</button>
  <span id="month"></span>
  <button onclick="showCalendar(Y,M+1)">+</button>
  <button onclick="showCalendar(Y,M-1)">-</button>
  <br><br>
  <table id="calendar">
  </table>

  <div class=" viewer">
    <h4>팀 일정</h4>
    <table class="table table-bordered table-hover table-striped tableset">
      <tr>
        <th>일정</th>
        <th>시작 시간</th>
        <th>종료 시간</th>
        <th>메모</th>
        <th>등록자</th>
      </tr>
      <?php
      $stmt=$pdo->prepare("SELECT event.`eventid`,event.`userid`,event.`regdate`,event.`title`,event.`datestart`,event.`dateend`,event.`memo`,user.`name` FROM `event` LEFT JOIN `user` ON event.userid=user.userid WHERE groupid=:groupid");
      $stmt->bindParam(':groupid',$_GET['id']);
      $stmt->execute();
      $list=$stmt->fetchAll(PDO::FETCH_ASSOC);
      for($i=0;$i<count($list);$i++)
      {
        $stime=substr($list[$i]['datestart'], 0,10)." _ ".substr($list[$i]['datestart'], 10);
        $etime=substr($list[$i]['dateend'], 0,10)." _ ".substr($list[$i]['dateend'], 10);
        echo "<tr onclick=\"event_worker({$list[$i]['eventid']},{$list[$i]['userid']});\" data-toggle=\"modal\" data-target=\"#eventmodal\"><td id=\"event_title_{$list[$i]['eventid']}\">".$list[$i]['title']."</td><td id=\"event_stime_{$list[$i]['eventid']}\">".$stime."</td><td id=\"event_etime_{$list[$i]['eventid']}\">".$etime."</td><td id=\"event_memo_{$list[$i]['eventid']}\">".$list[$i]['memo']."</td><td id=\"event_reguser_{$list[$i]['eventid']}\">".$list[$i]['name']."</td></tr>";
      }
      ?>
    </table>
  </div>

  <div class="viewer">
    <h4>팀 메모 보드</h4>
    <button class="btn btn-success" data-toggle="modal" data-target="#newmemo">새로운 팀 메모</button> <!-- Add Modal -->
    <!--Borad View Code-->
    <br><br>
    <table class="table table-bordered table-hover table-striped tableset">
      <tr>
        <th>메모</th>
        <th>개시자</th>
      </tr>
      <?php
      $stmt=$pdo->prepare("SELECT board.`boardid`,board.`memo`,user.`name`,board.`userid` FROM `board` LEFT JOIN `user` ON board.userid=user.userid WHERE groupid=:groupid");
      $stmt->bindParam(':groupid',$_GET['id']);
      $stmt->execute();
      $list=$stmt->fetchAll(PDO::FETCH_ASSOC);
      for($i=0;$i<count($list);$i++)
      {
        echo "<tr onclick=\"board_worker({$list[$i]['boardid']},{$list[$i]['userid']});\" data-toggle=\"modal\" data-target=\"#boardmodal\"><td id=\"board_".$list[$i]['boardid']."\">".$list[$i]['memo']."</td><td width=\"20%\">".$list[$i]['name']."</td></tr>";
      }
      ?>
    </table>
  </div>

  <!--Onclick Calendar Modal -->
  <div class="modal fade" id="newevent" tabindex="-1" role="dialog" aria-labelledby="NewEvent" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="NewEvent">새로운 일정</h4>
        </div>
        <div class="modal-body">
          <form id="eventform">
            <div class="form-group">
              <label for="EventName">일정 이름</label>
              <input type="text" class="form-control" id="EventName" name="eventname" placeholder="일정 이름을 입력하세요" required>
            </div>
            <div class="form-group">
              <label for="StartDay">시작일 날짜</label>
              <input type="date" class="form-control" id="StartDay" name="sday" required>
              <label for="StartTime">시작일 시간</label>
              <input type="time" class="form-control" id="StartTime" name="stime" required>
            </div>
            <div class="form-group" id="Endset">
              <label for="EndDay">종료일 날짜</label>
              <input type="date" class="form-control" id="EndDay" name="eday" required>
              <label for="EndTime">종료일 시간</label>
              <input type="time" class="form-control" id="EndTime" name="etime" required>
            </div>
            <div class="form-group">
              <label for="memo">메모</label>
              <textarea class="form-control" name="memo" id="eventmemo" rows="5"></textarea>
            </div>
            <button type="button" onclick="newevent()" data-dismiss="modal" class="btn btn-default">등록</button>
          </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
        </div>
      </div>
    </div>
  </div><!-- 모달 Out -->
  <!-- New Memo Modal -->
  <div class="modal fade" id="newmemo" tabindex="-1" role="dialog" aria-labelledby="NewMemo" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="NewMemo">새로운 메모</h4>
        </div>
        <div class="modal-body">
          <form id="memoform">
            <div class="form-group">
              <label for="memo">메모</label>
              <textarea class="form-control" name="memo" id="memo" rows="5" required></textarea>
            </div>
            <button type="button" onclick="newmemo()" data-dismiss="modal" class="btn btn-default">등록</button>
          </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
        </div>
      </div>
    </div>
  </div><!-- 모달 Out -->
  <!--Onclick Event List Modal-->
  <div class="modal fade" id="eventmodal" tabindex="-1" role="dialog" aria-labelledby="board" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="ViewEvent">일정</h4>
        </div>
        <div class="modal-body">
          <form>
            <input id="tar_eventid" value="" style="display: none">
            <span>일정 이름 : <div id="event_title"></div></span>
            <span>일정 시간 : <div id="event_time"></div></span>
            <span>메모 : <div id="event_memo"></div></span>
            <br>
            <button id="delevent_btn" type="button" onclick="event_delete();" class="btn btn-danger" data-dismiss="modal" style="display: none">삭제</button>
          </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
        </div>
      </div>
    </div>
  </div>
  <!--Onclick Board List Modal-->
  <div class="modal fade" id="boardmodal" tabindex="-1" role="dialog" aria-labelledby="board" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="ViewMemo">메모</h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="delmemo.php">
            <input id="tar_boardid"value="" style="display: none">
            <div id="board_context"></div><br>
            <button id="delmemo_btn" type="button" onclick="board_delete();" class="btn btn-danger" data-dismiss="modal" style="display: none">삭제</button>
          </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
        </div>
      </div>
    </div>
  </div>


<script type="text/javascript">
//1. #teamname 갱신
  var val_groupid=<?php echo $_GET['id']; ?>;
	function te(n)
  {
      var tm=M;
      if(tm<10) tm='0'+tm;
      var td=$(n).html();
      if(td<10) td='0'+td;
      var today = Y + "-" + tm + "-" + td;
      $("#StartDay").attr('value',today);
      $("#EndDay").attr('value',today);
  }
  function showCalendar(y, m)
  {
      if(m>12 || m<1 || y<2000 || y>2100) exit;
      var text = '\n<tr><td>';
      var d1 = (y+(y-y%4)/4-(y-y%100)/100+(y-y%400)/400+m*2+(m*5-m*5%9)/9-(m<3?y%4||y%100==0&&y%400?2:3:4))%7;
      for (i = 0; i < 42; i++) {
          if (i%7==0) text += '</tr>\n<tr>';
          if (i < d1 || i >= d1+(m*9-m*9%8)/8%2+(m==2?y%4||y%100==0&&y%400?28:29:30)) text += '<td> </td>';
          else text += '<td onclick="te(this)" data-toggle="modal" data-target="#newevent" class="vday"' + (i%7 ? '' : ' style="color:red;"') + ((i+1)%7 ? '' : ' style="color:blue;"') +'>' + (i+1-d1) + '</td>';
      } text=text + '</tr>\n';
      $('#calendar').html(text);
      $('#year').html("년도 : "+y);
      $('#month').html("월 : "+m);
      Y=y; M=m;
  }
  function newevent()
  {
    if($('#StartDay').val()>$('#EndDay').val() || ($('#StartDay').val()==$('#EndDay').val() && $('#StartTime').val()>$('#EndTime').val()))
    {
      $('#Endset').addClass('has-error');
      alert('종료시간이 시작시간보다 빠름니다.');
    }
    else
    {
      $('#loadimg').toggleClass("hide show");
      $.ajax({
        url:'calendar/newEvent.php',
        type:'post',
        dataType:'json',
        data:{
          'gid':val_groupid,
          'eventname':$('#EventName').val(),
          'sday':$('#StartDay').val(),
          'stime':$('#StartTime').val(),
          'eday':$('#EndDay').val(),
          'etime':$('#EndTime').val(),
          'memo':$('#eventmemo').val()
        },
        complete:function()
        {
          $('#main').load('calendar/teamcalendar.php?id='+val_groupid);
        }
      })
    }
  }
  function event_worker(eventid,reguserid)
  {
    var userid=<?php echo $_SESSION['userid']; ?>;
    $('#event_gid').attr('value',val_groupid);
    $('#event_title').html($('#event_title_'+eventid).html());
    $('#event_time').html($('#event_stime_'+eventid).html()+' ~ '+$('#event_etime_'+eventid).html());
    $('#event_memo').html($('#event_memo_'+eventid).html());
    if(reguserid==userid)
    {
      $('#delevent_btn').css('display','block');
      $('#tar_eventid').attr('value',eventid);
    }
  }
  function event_delete()
  {
    $('#loadimg').toggleClass("hide show");
    $.ajax({
      url:'calendar/delevent.php',
      type:'post',
      dataType:'json',
      data:{'tar_eventid':$('#tar_eventid').val(),'gid':val_groupid},
      complete:function()
      {
        $('#main').load('calendar/teamcalendar.php?id='+val_groupid);
      }
    })
  }
  function newmemo()
  {
    $('#loadimg').toggleClass("hide show");
    $.ajax({
      url:'calendar/newMemo.php',
      type:'post',
      dataType:'json',
      data:{
        'groupid':val_groupid,
        'memo':$('#memo').val()
      },
      complete:function()
      {
        $('#main').load('calendar/teamcalendar.php?id='+val_groupid);
      }
    })
  }
  function board_worker(boardid,reguserid)
  {
    var tar='#board_'+boardid;
    var userid=<?php echo $_SESSION['userid']; ?>;
    $('#memo_gid').attr('value',val_groupid);
    $('#board_context').html($(tar).html());
    if(reguserid==userid)
    {
      $('#delmemo_btn').css('display','block');
      $('#tar_boardid').attr('value',boardid);
    }
  }
  function board_delete()
  {
    $('#loadimg').toggleClass("hide show");
    $.ajax({
      url:'calendar/delmemo.php',
      type:'post',
      dataType:'json',
      data:{'tar_boardid':$('#tar_boardid').val(),'gid':val_groupid},
      complete:function()
      {
        $('#main').load('calendar/teamcalendar.php?id='+val_groupid);
      }
    })
  }
  function board_worker(boardid,reguserid)
  {
    var tar='#board_'+boardid;
    var userid=<?php echo $_SESSION['userid']; ?>;
    var gid=val_groupid;
    $('#memo_gid').attr('value',gid);
    $('#board_context').html($(tar).html());
    if(reguserid==userid)
    {
      $('#delmemo_btn').css('display','block');
      $('#tar_boardid').attr('value',boardid);
    }
  }
  function event_worker(eventid,reguserid)
  {
    var userid=<?php echo $_SESSION['userid']; ?>;
    var gid=val_groupid;
    $('#event_gid').attr('value',gid);
    $('#event_title').html($('#event_title_'+eventid).html());
    $('#event_time').html($('#event_stime_'+eventid).html()+' ~ '+$('#event_etime_'+eventid).html());
    $('#event_memo').html($('#event_memo_'+eventid).html());
    $('#event_reg').html($('#event_reguser_'+eventid).html());
    if(reguserid==userid)
    {
      $('#delevent_btn').css('display','block');
      $('#tar_eventid').attr('value',eventid);
    }
  }
  function checker()
  {
    $('#clock').css('display','block');
    var chk=<?php
    $stmt=$pdo->prepare("SELECT `level` FROM `joined` WHERE userid=:userid AND groupid=:groupid");
    $stmt->bindParam(':userid',$_SESSION['userid']);
    $stmt->bindParam(':groupid',$_GET['id']);
    $stmt->execute();
    $data=$stmt->fetch(PDO::FETCH_ASSOC);
    echo $data['level'];
    ?>;
    if(chk==1)
    {
      $('#loadimg').toggleClass("hide show");
      $('#main').load('calendar/teamedit.php?id='+val_groupid);
    }
    else alert("권환이 없습니다.");
  }
  var text=<?php echo "\"".$_SESSION['username']." 님\";";?>
  $('#_user').html(text);
  var name_calendar=<?php
  //Get Calendar Name
  $cm=$pdo->prepare("SELECT `name` FROM `group` WHERE groupid=:id");
  $cm->bindParam(':id',$_GET['id']);
  $cm->execute();
  $data=$cm->fetch(PDO::FETCH_ASSOC);
  echo "'".$data['name']."'";
  ?>;
  $('#teamname').html(name_calendar);
  $(document).ready(showCalendar(Y,M));
  var okevent=<?php
  if(isset($_SESSION['okevent']) && $_SESSION['okevent']==1)
  {
    unset($_SESSION['okevent']);
    echo 1;
  }
  else echo 0;
  ?>;
  if(okevent) $('#okevent').css('display','block');
  var okmemo=<?php
  if(isset($_SESSION['okmemo']) && $_SESSION['okmemo']==1)
  {
    unset($_SESSION['okmemo']);
    echo 1;
  }
  else echo 0;
  ?>;
  if(okmemo) $('#okmemo').css('display','block');
  var delmemo=<?php
  if(isset($_SESSION['delmemo']) && $_SESSION['delmemo']==1)
  {
    unset($_SESSION['delmemo']);
    echo 1;
  }else echo 0;?>;
  if(delmemo) $('#delmemo').css('display','block');
  var delevent=<?php
  if(isset($_SESSION['delevent']) && $_SESSION['delevent']==1)
  {
    unset($_SESSION['delevent']);
    echo 1;
  }else echo 0;?>;
  if(delevent) $('#delevent').css('display','block');
  var wrong=<?php
  if(isset($_SESSION['wrongcont']))
  {
    unset($_SESSION['wrongcont']);
    echo 1;
  }else echo 0;?>;
  if(wrong) $('#wrongcont').css('display','block');
</script>
