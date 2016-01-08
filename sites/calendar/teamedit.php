<?php
    require("../../lib/lib_teamcalendar.php");
    if(!islogin() || !isset($_GET['id']) || !isjoined($_GET['id']) || getlevel($_GET['id'])!=1) header('Location: ../../index.php');
    $pdo = pdoconnect();
?>
<img id="loadimg" src="calendar/ajax-loader.gif" alt="Loading.." class="hide"/>
<button onclick="prev();" class="btn btn-info btn-xs">돌아가기</button>
<h2>멤버</h2>
<table class="table table-sm">
    <tr>
        <th>이름</th>
        <th></th>
    </tr>
    <?php
    $stmt=$pdo->prepare("SELECT `user`.`name`,`user`.`userid` FROM `joined` LEFT JOIN `user` ON `joined`.`userid`=`user`.`userid` WHERE `joined`.`groupid`=:groupid AND `joined`.`level`=0");
    $stmt->bindParam(':groupid',$_GET['id']);
    $stmt->execute();
    $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
    for($i=0;$i<count($data);$i++)
    {
        echo "<tr><td>{$data[$i]['name']}</td><td><button onclick=\"member_out({$data[$i]['userid']});\" class=\"btn btn-info btn-xs\">추방</button></td></tr>";
    }?>
</table>
<h2>가입 신청</h2>
<table class="table table-sm">
    <tr>
        <th>이름</th>
        <th></th>
    </tr>
    <?php
    $stmt=$pdo->prepare("SELECT `user`.`name`,`request`.`requestid` FROM `request` LEFT JOIN `user` ON `request`.`userid`=`user`.`userid` WHERE `request`.`groupid`=:groupid AND `request`.`state`=0");
    $stmt->bindParam(':groupid',$_GET['id']);
    $stmt->execute();
    $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
    for($i=0;$i<count($data);$i++)
    {
        echo "<tr id=\"tr_request_{$data[$i]['requestid']}\"><td>{$data[$i]['name']}</td><td><button onclick=\"request_ok({$data[$i]['requestid']});\" class=\"btn btn-info btn-xs\">수락</button><span></span><button onclick=\"request_no({$data[$i]['requestid']});\" class=\"btn btn-info btn-xs\">거절</button></td></tr>";
    }
    ?>
</table>
<script type="text/javascript">
  function prev()
  {
    $('#loadimg').toggleClass("hide show");
    $('#main').load('calendar/teamcalendar.php?id='+<?php echo $_GET['id']; ?>);
  }
  function member_out(userid)
  {
    var str=prompt("정말로 추방 하시겠습니까? - 추방을 원하시면 YES를 입력하세요.");
    if(str=="YES")
    {
        $.ajax({
          url:'calendar/teameditlib/memberout.php',
          type:'POST',
          dataType:'json',
          data:{'userid': userid,'gid': <?php echo $_GET['id']; ?>},
          complete:function()
          {
            alert("추방 되었습니다.");refresh_memberlist();
          }
        })
    }
  }
  function request_ok(requestid)
  {
    $.ajax({
      url:'calendar/teameditlib/request_ok.php',
      type:'POST',
      dataType:'json',
      data:{'requestid':requestid},
      complete:function()
      {
        alert("수락되었습니다.");
        $('#tr_request_'+requestid).remove();
        refresh_memberlist();
      }
    })
  }
  function request_no(requestid)
  {
    $('#requestid').val(requestid);
    $.ajax({
      url:'calendar/teameditlib/request_no.php',
      type:'POST',
      dataType:'json',
      data:{'requestid':requestid},
      complete:function()
      {
        alert("거절 하였습니다.");
        $('#tr_request_'+requestid).remove();
        refresh_memberlist();
      }
    })
  }
  function refresh_memberlist()
  {
    $('#loadimg').toggleClass("hide show");
    $('#main').load('calendar/teamedit.php?id='+<?php echo $_GET['id']; ?>);
  }
</script>
