<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>商品一覧ページ</title>
  <link type="text/css" rel="stylesheet" href="./css/common.css">
</head>
<body>
  <header>
    <div class="header-box">
      <a href="top_controller.php">
        <img class="logo" src="./header_img/logo.png" alt="CodeSHOP">
      </a>
      <a class="nemu" href="logout_controller.php">ログアウト</a>
      <a href="cart_controller.php"><img class="cart" src="./header_img/cart.png"></a>
      <p class="nemu">ユーザー名：<?php print $_SESSION['user_name']; ?></p>
    </div>
  </header>
  <div class="content">
      <?php print $insert_cart; ?>
    <ul class="item-list">
      <?php
      foreach($item_data as $value) {
          if ($value['status'] === '1') {
      ?>
      <li>
        <div class="item">
          <form action="top_controller.php" method="post">
            <img class="img_size" src="./codeshop_img/<?php print $value['img']; ?>" >
            <div class="item-info">
              <span class="item-name"><?php print htmlspecialchars($value['name']); ?></span>
              <span class="item-price">¥<?php print htmlspecialchars($value['price']); ?></span>
            </div>
            <?php
            sold_out_check($value);
            ?>
            <input type="hidden" name="item_id" value="<?php print $value['id']; ?>">
            <input type="hidden" name="sql_kind" value="insert_cart">
          </form>
        </div>
      </li>
      <?php
          }
      }
      ?>
    </ul>
  </div>
</body>
</html>
