<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ログインページ</title>
  <link type="text/css" rel="stylesheet" href="common.css">
</head>
<body>
  <header>
    <div class="header-box">
      <a href="top_controller.php">
        <img class="logo" src="./codeshop_img/logo.png" alt="CodeSHOP">
      </a>
    </div>
  </header>
  <div class="content">
    <div class="login">
      <?php
      foreach($err_msg as $value) {
          print '<p class="err">' . $value .'</p>';
      }
      ?>
      <form method="post">
        <div>ユーザー名：<input type="text" name="user_name" placeholder="ユーザー名"></div>
        <div>パスワード：<input type="password" name="password" placeholder="パスワード">
        <div><input type="submit" value="ログイン">
      </form>
      <div class="account-create">
        <a href="register_controller.php">ユーザーの新規作成</a>
      </div>
    </div>
  </div>
</body>
</html>
