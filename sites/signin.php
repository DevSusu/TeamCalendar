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
    <title>Team Calendar - Sign in</title>
    <meta name="google-signin-client_id" content="474088374198-2gue2dceg8lor2q8j1dvpuhbi3vou3nk.apps.googleusercontent.com">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <!-- 부트스트랩 -->

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link herf="../css/signin.css" rel="stylesheet">
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
      <div class="alert alert-danger alert-dismissible" role="alert" style="display: none" id="worngemail">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>이미 가입된 이메일입니다!</strong> 이미 등록되어 있는 이메일입니다.
      </div>
      <div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
      <form method="POST" onsubmit="return submit_worker(this);" action="signin_worker.php">
        <div class="form-group">
          <label for="name" class="f_label">이름</label>
          <input type="text" class="form-control" name="name" placeholder="이름을 입력하세요" required>
        </div>
        <div class="form-group">
          <label for="email" class="f_label">이메일</label>
          <input type="email" class="form-control" name="email" placeholder="이메일" required>
        </div>
        <div class="form-group">
          <label for="pw" class="f_label">비밀번호</label>
          <input type="password" class="form-control" id="pw" name="pw" placeholder="비밀번호" required>
        </div>
        <div class="form-group">
          <label for="repw" class="f_label">비밀번호 재확인</label>
          <input type="password" class="form-control" id="repw" name="repw" placeholder="비밀번호 재확인" required>
        </div>
        <button type="submit" class="btn btn-default">제출</button>
      </form>
    </div>

    <footer>
      <?php footer_out() ?>
    </footer>
    <!-- jQuery (부트스트랩의 자바스크립트 플러그인을 위해 필요합니다) -->
    <script src="../js/jquery-2.1.4.min.js"></script>
    <!-- 모든 컴파일된 플러그인을 포함합니다 (아래), 원하지 않는다면 필요한 각각의 파일을 포함하세요 -->
    <script src="../js/bootstrap.min.js"></script>
    <script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha3.js"></script>
    <script>
      function onSignIn(googleUser) {
        $.ajax({
          url:'signin_worker.php',
          type:'post',
          dataType:'json',
          data:{
            'name':profile.getName(),
            'Email':profile.getEmail(),
            'id_token':id_token
          },
          complete:function()
          {
            loaction.href("welcome.php");
          }
        })
      }
      function submit_worker()
      {
        if($('#pw').val()==$('#repw').val())
        {
          $('#pw').css('display','none');
          var hash=CryptoJS.SHA3($('#pw').val());
          $('#pw').val(hash);
          return true;
        }
        else
        {
          alert('비밀번호란과 재확인란의 비밀번호를 동일하게 입력해주세요.');
          return false;
        }
      }
      var worngemail=<?php
      if(isset($_SESSION['worngemail']) && $_SESSION['worngemail']) echo 1;
      else echo 0;
      unset($_SESSION['worngemail']);
      ?>;
      if(worngemail) $('#worngemail').css('display','block');
    </script>
  </body>
</html>
