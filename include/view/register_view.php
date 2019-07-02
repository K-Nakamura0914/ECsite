<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ユーザ登録ページ</title>
  <link rel="stylesheet" href="css/common.css">
</head>
<body>
  <header>
    <div class="header-box">
      <a href="top.php">
        <img class="logo" src="./header_img/logo.png" alt="CodeSHOP">
      </a>
    </div>
  </header>
  <div class="content">
    <div class="register">
      <?php
      print $msg;
      foreach($err_msg as $value) {
          print '<p class="err">' . $value .'</p>';
      }
      ?>
      <form method="post" action="register.php">
        <div>ユーザー名：<input type="text" name="user_name" placeholder="ユーザー名"></div>
        <div>パスワード：<input type="password" name="password" placeholder="パスワード">
        <div><input type="submit" value="登録">
      </form>
      <div class="login-link"><a href="index.php">ログインページに移動する</a></div>
    </div>
  </div>
</body>
</html>
