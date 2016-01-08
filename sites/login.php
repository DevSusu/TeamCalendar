<?php
    require("../lib/lib_teamcalendar.php");
    if(islogin()) header('Location: ../index.php');
?>

<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 위 3개의 메타 태그는 *반드시* head 태그의 처음에 와야합니다; 어떤 다른 콘텐츠들은 반드시 이 태그들 *다음에* 와야 합니다 -->
    <title>Team Calendar - Login</title>

    <!-- 부트스트랩 -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link herf="../css/login.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/home.css">

    <!-- IE8 에서 HTML5 요소와 미디어 쿼리를 위한 HTML5 shim 와 Respond.js -->
    <!-- WARNING: Respond.js 는 당신이 file:// 을 통해 페이지를 볼 때는 동작하지 않습니다. -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
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
            <a class="navbar-brand" href="../index.php">Team Calendar</a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
              <li id="_in"><a href="login.php">Login</a></li>
              <li><a href="signin.php">Sign in</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
    </nav>

    <div class="container">
      <form class="form-signin">
        <h2 class="form-signin-heading">Login</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="pw" class="form-control" placeholder="Password" required>
        <br>
        <button class="btn btn-lg btn-primary btn-block" onclick="worker_submit();">Login</button>
      </form>
    </div>

    <footer>
      <?php footer_out(); ?>
    </footer>
    <!-- jQuery (부트스트랩의 자바스크립트 플러그인을 위해 필요합니다) -->
    <script src="../js/jquery-2.1.4.min.js"></script>
    <!-- 모든 컴파일된 플러그인을 포함합니다 (아래), 원하지 않는다면 필요한 각각의 파일을 포함하세요 -->
    <script src="../js/bootstrap.min.js"></script>
    <script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha3.js"></script>
    <script type="text/javascript">
      function worker_submit()
      {
        $('#inputPassword').css('display','none');
        var pw=CryptoJS.SHA3($('#inputPassword').val());
        $('#inputPassword').val(pw);
        $.ajax({
          url:'./login_worker.php',
          type:'POST',
          data:$('form').serialize(),
          success:function(data)
          {
            $('.alert').css('display','none');
            if(data==0)
            {
              location.href("../index.php");
            }
          }
        })
      }
    </script>

  </body>
</html>
