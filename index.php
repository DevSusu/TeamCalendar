<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 위 3개의 메타 태그는 *반드시* head 태그의 처음에 와야합니다; 어떤 다른 콘텐츠들은 반드시 이 태그들 *다음에* 와야 합니다 -->
    <title>Team Calendar</title>
    <!-- 부트스트랩 -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  </head>
  <body>

    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">Team Calendar</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown" id="dm" style="display: none">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">캘린더 <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="sites/calendar.php#mycalendar">나의 캘린더</a></li>
                <li><a href="sites/calendar.php#teamcalendar">팀 캘린더</a></li>
                <li role="presentation" class="divider"></li>
                <?php
                require("lib/lib_teamcalendar.php");
                if(isset($_SESSION['userid']))
                {
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
                    echo "<li><a href=\"sites/calendar.php#myteam".$id."\">".$name['name']."</a></li>";
                  }
                }
                ?>
              </ul>
            </li>
            <li><a href="sites/calendar.php#userpage" id="_user"></a></li>
          	<li id="_in"><a href="sites/login.php">Login</a></li>
          	<li id="out"><a href="sites/logout.php">Logout</a></li>
          	<li id="sign"><a href="sites/signin.php">Sign in</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div><!--/.container-fluid -->
    </nav>
    <div class="jumbotron">
      <div class="container">
        <h1>Team Calendar</h1>
        <p class="lead">Team Calendar을 통해서 팀원들과 일정을 공유하고 모임 일정을 정할 수 있습니다.</div>
        <p class="lead" id="sign_btn"><a href="sites/signin.php" class="btn btn-lg btn-primary">지금 가입하기</a></p>
      </div>
    </div>


    <footer>
    	<?php footer_out(); ?>
    </footer>
    <!-- jQuery (부트스트랩의 자바스크립트 플러그인을 위해 필요합니다) -->
    <script src="js/jquery-2.1.4.min.js"></script>
    <!-- 모든 컴파일된 플러그인을 포함합니다 (아래), 원하지 않는다면 필요한 각각의 파일을 포함하세요 -->
    <script src="js/bootstrap.min.js"></script>
    <script>
	    var chk=<?php
	    if(isset($_SESSION['userid'])) echo 1;
	    else echo 0;
	    ?>;
	    if(chk==1)
    	{
    		$('#_in').css('display','none');
    		$('#sign').css('display','none');
        $('#dm').css('display','block');
        var text=<?php if(isset($_SESSION['username'])) echo "\"".$_SESSION['username']." 님\";"; ?>
        $('#_user').html(text);
    	}
	    if(chk==0)
      {
        $('#sign_btn').css('display','block');
        $('#out').css('display','none');
      }
    </script>
  </body>
</html>
