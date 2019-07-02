<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ショッピングカートページ</title>
  <link type="text/css" rel="stylesheet" href="./css/common.css">
</head>
<body>
  <header>
    <div class="header-box">
      <a href="top.php">
        <img class="logo" src="./header_img/logo.png" alt="CodeCamp SHOP">
      </a>
      <a class="nemu" href="logout.php">ログアウト</a>
      <a href="cart.php"><img class="cart" src="./header_img/cart.png"></a>
      <p class="nemu">ユーザー名：<?php print $_SESSION['user_name']; ?></p>
    </div>
  </header>
  <div class="content">
    <h1 class="title">ショッピングカート</h1>
    <?php
    print $empty;
    print $msg;
    foreach ($err_msg as $value) {
        print '<p class="err">' . $value . '</p>';
    }
    ?>
    <div class="cart-list-title">
      <span class="cart-list-price">価格</span>
      <span class="cart-list-num">数量</span>
    </div>
    <ul class="cart-list">
        <?php
        foreach ($cart_list as $value) {
        ?>
        <li>
        <div class="cart-item">
          <img class="cart-item-img" src="./product_img/<?php print $value['img']; ?>">
          <span class="cart-item-name"><?php print htmlspecialchars($value['name']); ?></span>
          <form class="cart-item-del" action="cart.php" method="post">
            <input type="submit" value="削除" name='delete'>
            <input type="hidden" name="item_id" value="<?php print $value['id']; ?>">
            <input type="hidden" name="sql_kind" value="delete_cart">
          </form>
          <span class="cart-item-price">¥ <?php print $value['price']; ?></span>
          <form class="form_select_amount" id="form_select_amount159" action="cart.php" method="post">
            <input type="text" class="cart-item-num2" min="0" name="select_amount" value="<?php print $value['amount']; ?>">個&nbsp;<input type="submit" value="変更する" name="change">
            <input type="hidden" name="item_id" value="<?php print $value['id']; ?>">
            <input type="hidden" name="sql_kind" value="change_cart">
          </form>
        </div>
        </li>
        <?php
        }
        ?>
    </ul>
    <div class="buy-sum-box">
      <span class="buy-sum-title">合計</span>
      <span class="buy-sum-price">¥<?php print $total_price; ?></span>
    </div>
    <div>
      <form action="finish.php" method="post">
      <?php
      foreach ($cart_list as $value) {
      ?>
        <input type="hidden" name="item_id_<?php print $i; ?>" value="<?php print $value['id']; ?>">
        <input type="hidden" name="select_amount_<?php print $i; ?>" value="<?php print $value['amount']; ?>">
        <input type="hidden" name="now_status_<?php print $i; ?>" value="<?php print $value['status']; ?>">
      <?php
      $i++;
      }
      ?>
        <input name='buy_btn' class="buy-btn" type="submit" value="購入する">
      </form>
    </div>
  </div>
</body>
</html>
