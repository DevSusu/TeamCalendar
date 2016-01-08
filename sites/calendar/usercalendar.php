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

<!-- Message Alert -->
<h1>나의 캘린더</h1>
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
  <h4>개인 일정</h4>
  <table class="table table-bordered table-hover table-striped tableset">
    <tr>
      <th>일정</th>
      <th>시작 시간</th>
      <th>종료 시간</th>
      <th>메모</th>
    </tr>
    <?php
    require("../../lib/lib_teamcalendar.php");
    $pdo = pdoconnect();
    $stmt=$pdo->prepare("SELECT event.`eventid`,event.`userid`,event.`regdate`,event.`title`,event.`datestart`,event.`dateend`,event.`memo` FROM `event` LEFT JOIN `user` ON event.userid=user.userid WHERE user.userid=:userid AND event.groupid='-1'");
    $stmt->bindParam(':userid',$_SESSION['userid']);
    $stmt->execute();
    $list=$stmt->fetchAll(PDO::FETCH_ASSOC);
    for($i=0;$i<count($list);$i++)
    {
      $stime=substr($list[$i]['datestart'], 0,10)." _ ".substr($list[$i]['datestart'], 10);
      $etime=substr($list[$i]['dateend'], 0,10)." _ ".substr($list[$i]['dateend'], 10);
      echo "<tr onclick=\"event_worker({$list[$i]['eventid']},{$list[$i]['userid']});\" data-toggle=\"modal\" data-target=\"#eventmodal\"><td id=\"event_title_{$list[$i]['eventid']}\">".$list[$i]['title']."</td><td id=\"event_stime_{$list[$i]['eventid']}\">".$stime."</td><td id=\"event_etime_{$list[$i]['eventid']}\">".$etime."</td><td id=\"event_memo_{$list[$i]['eventid']}\">".$list[$i]['memo']."</td></tr>";
    }
    ?>
  </table>
</div>
<div class="viewer">
  <h4>메모 보드</h4>
  <button class="btn btn-success" data-toggle="modal" data-target="#newmemo">새로운 메모</button> <!-- Add Modal -->
  <!--Borad View Code-->
  <br><br>
  <table class="table table-bordered table-hover table-striped tableset">
    <tr>
      <th>메모</th>
      <th>등록일</th>
    </tr>
    <?php
    $stmt=$pdo->prepare("SELECT board.`boardid`,board.`memo`,user.`name`,board.`userid`,board.`regdate` FROM `board` LEFT JOIN `user` ON board.userid=user.userid WHERE user.userid=:userid AND board.groupid=-1");
    $stmt->bindParam(':userid',$_SESSION['userid']);
    $stmt->execute();
    $list=$stmt->fetchAll(PDO::FETCH_ASSOC);
    for($i=0;$i<count($list);$i++)
    {
      echo "<tr onclick=\"board_worker({$list[$i]['boardid']},{$list[$i]['userid']});\" data-toggle=\"modal\" data-target=\"#boardmodal\"><td id=\"board_".$list[$i]['boardid']."\">".$list[$i]['memo']."</td><td width=\"20%\">".$list[$i]['regdate']."</td></tr>";
    }
    ?>
  </table>
</div>
<div class="viewer">
  <h4>할 일</h4>
  <button class="btn btn-success" data-toggle="modal" data-target="#newtodo">새로운 할 일</button>
  <br><br>
  <table class="table table-bordered table-hover table-striped tableset">
    <tr>
      <th>할 일</th>
      <th>상태</th>
      <th>마감일</th>
    </tr>
    <?php
    $stmt=$pdo->prepare("SELECT `context`,`todoid`,`deadline`,`state` FROM `todo` WHERE userid=:userid");
    $stmt->bindParam(':userid',$_SESSION['userid']);
    $stmt->execute();
    $list=$stmt->fetchAll(PDO::FETCH_ASSOC);
    for($i=0;$i<count($list);$i++)
    {
      $state="";
      if($list[$i]['state']==0) $state="미완료";
      else if($list[$i]['state']==1) $state="완료";
      echo "<tr onclick=\"todo_modalworker({$list[$i]['todoid']});\" data-toggle=\"modal\" data-target=\"#todomodal\"><td id=\"todolist_context_{$list[$i]['todoid']}\">{$list[$i]['context']}</td><td id=\"todolist_state_{$list[$i]['todoid']}\">{$state}</td><td id=\"todolist_daedline_{$list[$i]['todoid']}\">{$list[$i]['deadline']}</td></tr>";
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
    <div class="modal fade" id="newtodo" tabindex="-1" role="dialog" aria-labelledby="NewTodo" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">새로운 할 일</h4>
          </div>
          <div class="modal-body">
            <form id="form_newtodo">
              <div class="form-group">
                <label for="context">내용</label>
                <input id="context" class="form-control" rows="5" name="context" type="text" placeholder="해야할 일을 입력해주세요." required>
              </div>
              <div class="form-group">
                <label for="deadline_date">마감일 날짜</label>
                <input id="deadline_date" class="form-control" name="deadline_date" type="date">
                <label for="deadline_time">마감일 시간</label>
                <input id="deadline_time" class="form-control" name="deadline_time" type="time">
              </div>
            </form>
            <button onclick="newtodo_checker();" data-dismiss="modal" class="btn btn-default">등록</button>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="todomodal" tabindex="-1" role="dialog" aria-labelledby="Todo" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">할 일</h4>
          </div>
          <div class="modal-body">
            <span id="todoid" class="hide"></span>
            <div>내용 :</div>
            <div id="todo_context"></div><br>
            <div>기한 :</div>
            <div id="todo_deadline"></div>
            <hr>
            <div class="radio">
              <label><input type="radio" name="todo_state" id="todo_state_O"> 완료</label>
              <label><input type="radio" name="todo_state" id="todo_state_X"> 미완료</label>
            </div>
            <button onclick="todo_state();" data-dismiss="modal" type="button" class="btn btn-sm btn-default" name="adjust">적용</button>
            <button onclick="todo_delete();" data-dismiss="modal" type="button" class="btn btn-sm btn-danger" name="delete">삭제</button>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
//1. #teamname 갱신
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
          'gid':-1,
          'eventname':$('#EventName').val(),
          'sday':$('#StartDay').val(),
          'stime':$('#StartTime').val(),
          'eday':$('#EndDay').val(),
          'etime':$('#EndTime').val(),
          'memo':$('#eventmemo').val()
        },
        complete:function()
        {
          $('#main').load('calendar/usercalendar.php');
        }
      })
    }
  }
  function event_worker(eventid,reguserid)
  {
    var userid=<?php echo $_SESSION['userid']; ?>;
    $('#event_gid').attr('value',-1);
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
      data:{'tar_eventid':$('#tar_eventid').val(),'gid':-1},
      complete:function()
      {
        $('#main').load('calendar/usercalendar.php');
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
        'groupid':-1,
        'memo':$('#memo').val()
      },
      complete:function()
      {
        $('#main').load('calendar/usercalendar.php');
      }
    })
  }
  function board_worker(boardid,reguserid)
  {
    var tar='#board_'+boardid;
    var userid=<?php echo $_SESSION['userid']; ?>;
    $('#memo_gid').attr('value',-1);
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
      data:{'tar_boardid':$('#tar_boardid').val(),'gid':-1},
      complete:function()
      {
        $('#main').load('calendar/usercalendar.php');
      }
    })
  }
  function newtodo_checker()
  {
    if($('#deadline_time').val() && !$('#deadline_date').val()) alert("마감 시간을 설정하시려면 마감 날짜를 설정해주세요.");
    else
    {
      $('#loadimg').toggleClass("hide show");
      $.ajax({
        url:'calendar/newtodo.php',
        type:'post',
        dataType:'json',
        data:{
          'context':$('#context').val(),
          'deadline_date':$('#deadline_date').val(),
          'deadline_time':$('#deadline_time').val()
        },
        complete:function()
        {
          $('#main').load('calendar/usercalendar.php');
        }
      })
    }
  }
  function todo_modalworker(todoid)
  {
    $('#todoid').html(todoid);
    $('#todo_context').html($('#todolist_context_'+todoid).html());
    $('#todo_deadline').html($('#todolist_daedline_'+todoid).html());
    if($('#todolist_state_'+todoid).html()=="완료") $('#todo_state_O').attr('checked',true);
    else $('#todo_state_X').attr('checked',true);
  }
  function todo_state()
  {
    var state;
    if($('#todo_state_O').is(':checked')) state=1;
    else state=0;
    $('#loadimg').toggleClass("hide show");
    $.ajax({
      url:'calendar/todo_state.php',
      type:'POST',
      dataType:'json',
      data:{'todoid':$('#todoid').html(),'state':state},
      complete:function()
      {
        $('#main').load('calendar/usercalendar.php');
      }
    })
  }
  function todo_delete()
  {
    $('#loadimg').toggleClass("hide show");
    $.ajax({
      url:'calendar/todo_delete.php',
      type:'POST',
      dataType:'json',
      data:{'todoid':$('#todoid').html()},
      complete:function()
      {
        $('#main').load('calendar/usercalendar.php');
      }
    })
  }
  var text=<?php echo "\"".$_SESSION['username']." 님\";";?>
  $('#_user').html(text);
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
</script>
