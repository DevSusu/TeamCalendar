<?php
    require("../lib/lib_teamcalendar.php");
    if(!islogin()) header('Location: ../index.php');
?>
<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 위 3개의 메타 태그는 *반드시* head 태그의 처음에 와야합니다; 어떤 다른 콘텐츠들은 반드시 이 태그들 *다음에* 와야 합니다 -->
    <title id="title"></title>
    <!-- 부트스트랩 -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/userpage.css">
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  </head>
  <body>

    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="../index.php">Team Calendar</a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
              <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">캘린더 <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#mycalendar">나의 캘린더</a></li>
                <li><a href="#teamcalendar">팀 캘린더</a></li>
                <li role="presentation" class="divider"></li>
                <?php
                $pdo = pdoconnect();
                $stmt=$pdo->prepare("SELECT `groupid` FROM `joined` WHERE userid=:id");
                $stmt->bindParam(':id',$_SESSION['userid']);
                $stmt->execute();
                $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
                for($i=0;$i<count($data);$i++)
                {
                  $stmt=$pdo->prepare("SELECT `name` FROM `group` WHERE groupid=:id");
                  $stmt->bindParam(':id',$id);
                  $id=$data[$i]['groupid'];
                  $stmt->execute();
                  $name=$stmt->fetch(PDO::FETCH_ASSOC);
                  echo "<li><a href=\"#myteam{$id}\">{$name['name']}</a></li>";
                }
                ?>
              </ul>
            </li>
              <li><a id="_user" href="#userpage"></a></li>
              <li id="out"><a href="logout.php">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li id="sidebar_user"><a href="#userpage">내 정보</a></li>
            <li id="sidebar_my"><a href="#mycalendar">나의 캘린더</a></li>
            <li id="sidebar_team"><a href="#teamcalendar">팀 캘린더</a></li>
            <!-- 사용자 팀 캘린더 링크 -->
          </ul>
        </div>
        <div id="main" class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <img id="loadimg" src="calendar/ajax-loader.gif" alt="Loading.." class="hide"/>
        </div>
      </div>
    </div>

    <footer>
    	<?php footer_out(); ?>
    </footer>

    <!-- jQuery (부트스트랩의 자바스크립트 플러그인을 위해 필요합니다) -->
    <script src="../js/jquery-2.1.4.min.js"></script>
    <!-- 모든 컴파일된 플러그인을 포함합니다 (아래), 원하지 않는다면 필요한 각각의 파일을 포함하세요 -->
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/holder.min.js"></script>
    <script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha3.js"></script>
    <script type="text/javascript">
    var text=<?php echo "\"".$_SESSION['username']." 님\";"; ?>
    $('#_user').html(text);
    $('a').click(function(){
      var tar=$(this).attr('href');
      if(tar=="#userpage") {$('#main').load('./userpage.php');$('#loadimg').toggleClass("hide show");}
      else if(tar=="#teamcalendar") {$('#main').load('calendar/teamcalendaredit.php');$('#loadimg').toggleClass("hide show");}
      else if(tar=="#mycalendar") {$('#main').load('calendar/usercalendar.php');$('#loadimg').toggleClass("hide show");}
      else if(tar.indexOf('myteam')==1)
      {
        var teamid=tar.slice(7);
        $('#loadimg').toggleClass("hide show");
        $('#main').load('calendar/teamcalendar.php?id='+teamid);
      }
    });
    $(document).ready(function(){
      $('#loadimg').toggleClass("hide show");
      var para = document.location.href;
      var tar=para.slice(para.indexOf('#'));
      if(tar=="#userpage") {$('#main').load('./userpage.php');$('#loadimg').toggleClass("hide show");}
      else if(tar=="#teamcalendar") {$('#main').load('calendar/teamcalendaredit.php');$('#loadimg').toggleClass("hide show");}
      else if(tar=="#mycalendar") {$('#main').load('calendar/usercalendar.php');$('#loadimg').toggleClass("hide show");}
      else if(tar.indexOf('myteam')==1)
      {
        var teamid=tar.slice(7);
        $('#main').load('calendar/teamcalendar.php?id='+teamid);
      }$('#loadimg').toggleClass("hide show");
    });
    </script>
  </body>
</html>
