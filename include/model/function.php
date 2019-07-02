<?php

// DB接続
function db_connect() {

    // コネクション取得
    if (!$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME)) {
        die('error: ' . mysqli_connect_error());
    }

    // 文字コードセット
    mysqli_set_charset($link, DB_CHARACTER_SET);

    return $link;
}

// 商品取得
function get_item($link) {
    mysqli_set_charset($link, DB_CHARACTER_SET);
    $data = array();
    $sql = 'SELECT item.id, item.img, item.name, item.price, stock.stock, item.status FROM item JOIN stock ON item.id = stock.item_id';
    $result = mysqli_query($link, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $data[] = $row;
    }
    mysqli_free_result($result);
    return $data;
}

//ユーザー一覧取得
function get_user($link) {
    mysqli_set_charset($link, DB_CHARACTER_SET);
    $data = array();
    $sql = 'SELECT user_name, created_date, id FROM user';
    $result = mysqli_query($link, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $data[] = $row;
    }
    mysqli_free_result($result);
    return $data;
}

// DB切断
function db_close($link) {
    mysqli_close($link);
}

//　文頭・文末の空白を取り除く
function space_trim($str) {
    $trim = str_replace('　', ' ', $str);
    $trim = trim($trim);
    return $trim;
}

// 「商品の登録」エラーチェック
function err_check($item_name, $price, $stock, $img, $status) {
    $error = array();

    // 文頭・文末の空白を取り除く
    $item_name = space_trim($item_name);
    $price = space_trim($price);
    $stock = space_trim($stock);

    if (mb_strlen($item_name) === 0) {
        $error[] = '名前を入力してください。';
    }

    if (mb_strlen($price) === 0) {
        $error[] = '値段を入力してください。';
    } else if (preg_match('/^(0|[1-9]\d*)$/', $price) !== 1) {
        $error[] = '値段は半角数字を入力してください。';
    }

    if (mb_strlen($stock) === 0) {
        $error[] = '個数を入力してください。';
    } else if (preg_match('/^(0|[1-9]\d*)$/', $stock) !== 1) {
        $error[] = '個数は半角数字を入力してください。';
    }

    if ($status !== '1' && $status !== '0') {
        $error[] = '公開もしくは非公開を設定してください。';
    }

    if (mb_strlen($img) === 0) {
        $error[] = 'ファイルを選択してください。';
    } else if (preg_match('/JPEG$|PNG$|jpeg$|png$/', $img) !== 1) {
        $error[] = 'ファイル形式が異なります。画像ファイルはJPEG又はPNGのみ利用可能です。';
    }

    return $error;
}

// リクエストメソッドを取得
function get_request_method() {
   return $_SERVER['REQUEST_METHOD'];
}

// 入力受け取り
function get_post_data($key) {
   $str = '';
   if (isset($_POST[$key]) === TRUE) {
       $str = $_POST[$key];
   }
   return $str;
}

// ファイル受け取り
function get_file_data() {
    $str = '';
    $new_filename = make_rand_str(); //ランダムな英数字列設定
    if (isset($_FILES['new_img']) === TRUE) {
        $str = $_FILES['new_img']['name'];
    }

    if (preg_match('/JPEG$|jpeg$/', $str) === 1) { //拡張子がjpegのとき
        $str = $new_filename . '.jpeg';
    } else if (preg_match('/PNG$|png$/', $str) === 1) { //拡張子がpngのとき
        $str = $new_filename . '.png';
    }

    return $str;
}

// 画像一時保存場所
function get_file_tmp() {
    $tmp = '';
    if (isset($_FILES['new_img']) === TRUE) {
        $tmp = $_FILES['new_img']['tmp_name'];
    }
    return $tmp;
}

//ランダムで英数字列作成
function make_rand_str() {
    $str = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPUQRSTUVWXYZ';
    $str_r = substr(str_shuffle($str), 0, 10);
    return $str_r;
}

// 商品追加
function insert_item($item_name, $price, $img, $status, $stock, $created_date, $link, $tmp) {
    $sql = 'INSERT INTO item(name, price, img, status, created_date, updated_date) VALUES (\'' . $item_name . '\',' . $price . ',\'' . $img . '\',' . $status . ',\'' . $created_date . '\',\'' . $created_date . '\')';
    mysqli_query($link, $sql);
    $item_id = mysqli_insert_id($link);
    $sql2 = 'INSERT INTO stock(item_id, stock, created_date, updated_date) VALUES (' . $item_id . ',' . $stock . ',\'' . $created_date . '\',\'' . $created_date . '\')';
    mysqli_query($link, $sql2);
    move_uploaded_file($tmp, './header_img/' . $img);
}

// 在庫数変更
function stock_change($update_stock, $item_id, $link, $now_date) {
    $sql = 'UPDATE stock SET stock = ' . $update_stock . ' WHERE item_id =' . $item_id;
    mysqli_query($link, $sql);
    updated_date($now_date, $item_id, $link);
}

//更新日時を更新
function updated_date($now_date, $item_id, $link) {
    $sql = 'UPDATE item SET updated_date = \'' . $now_date . '\' WHERE id = ' . $item_id;
    $sql2 = 'UPDATE stock SET updated_date  = \'' . $now_date . '\' WHERE item_id = ' . $item_id;
    mysqli_query($link, $sql);
    mysqli_query($link, $sql2);
}

// 在庫数変更のエラーチェック
function stock_change_err_check($update_stock) {
    $error = array();

    //空白を取り除く
    $update_stock = str_replace('　', ' ', $update_stock);
    $update_stock = trim($update_stock);

    if (mb_strlen($update_stock) === 0) {
        $error[] = '個数を入力してください。';
    } else if (preg_match('/^(0|[1-9]\d*)$/', $update_stock) !== 1) {
        $error[] = '個数は半角数字を入力してください。';
    }

    return $error;
}

//ステータス変更エラーチェック
function change_status_err_check($now_status) {
    $error = array();

    if ($now_status !== '1' && $now_status !== '0') {
        $error[] = '公開もしくは非公開を設定してください。';
    }

    return $error;
}

//ステータス変更
function change_status($now_status, $item_id, $link) {
    $sql = '';

    if ($now_status === '0') { //公開に設定する場合
        $sql = 'UPDATE item SET status = 1 WHERE id =' . $item_id;
    } else if ($now_status === '1'){ //非公開にする場合
        $sql = 'UPDATE item SET status = 0 WHERE id =' . $item_id;
    }

    mysqli_query($link, $sql);
}

//公開・非公開変更時の背景色変更
function change_display($value) {
    if ($value['status'] === '1') {
        print 'class="status_true"';
    } else {
        print 'class="status_false"';
    }
}

//ボタンテキスト変更
function change_status_button($value) {
    if ($value['status'] === '1') {
        print '公開 → 非公開';
    } else {
        print '非公開 → 公開';
    }
}

//商品削除
function delete_item($item_id, $link, $now_img) {
    $sql = 'DELETE FROM stock WHERE item_id = ' . $item_id;
    $sql2 = 'DELETE FROM item WHERE id = ' . $item_id;
    mysqli_query($link, $sql);
    mysqli_query($link, $sql2);
    unlink($now_img);
}

//ユーザー新規作成エラーチェック
function register_err_check($user_name, $password, $link) {
    $error = array();

    //文頭・文末の空白を取り除く
    $user_name = space_trim($user_name);
    $password = space_trim($password);

    //ユーザーデータセット
    $user_data = duplicate_check($user_name, $link);

    //エラーチェック
    if (mb_strlen($user_name) < 6) {
        $error[] = 'ユーザー名は6文字以上の文字を入力してください。';
    } else if (preg_match('/^[0-9a-zA-Z]{6,}$/', $user_name) !== 1) {
        $error[] = 'ユーザ名は半角英数字を入力してください。';
    }

    if (mb_strlen($password) < 6) {
        $error[] = 'パスワードは6文字以上の文字を入力してください。';
    } else if (preg_match('/^[0-9a-zA-Z]{6,}$/', $password) !== 1) {
        $error[] = 'パスワードは半角英数字を入力してください。';
    }

    if ($user_data[0] === '1') {
        $error[] = '同じユーザー名が既に登録されています。';
    }

    return $error;
}

//ユーザー名重複チェック
function duplicate_check($user_name, $link) {
    mysqli_set_charset($link,DB_CHARACTER_SET);
    $sql = 'SELECT COUNT(user_name), password, id FROM user WHERE user_name = \'' . $user_name . '\'';
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_array($result);
    mysqli_free_result($result);
    return $row;
}

//DBにユーザー名とパスワードを保存
function insert_new_user($user_name, $password, $now_date, $link) {
    mysqli_set_charset($link, DB_CHARACTER_SET);
    $sql = 'INSERT INTO user(user_name, password, created_date, updated_date) VALUES (\'' . $user_name . '\',\'' . $password . '\',\'' . $now_date . '\',\'' . $now_date . '\')';
    mysqli_query($link, $sql);
}

// ログインエラーチェック
function login_err_check($user_name, $password, $link) {
    $error = array();

    //文頭・文末の空白を取り除く
    $user_name = space_trim($user_name);
    $password = space_trim($password);

    //ユーザーデータセット
    $user_data = duplicate_check($user_name, $link);

    //エラーチェック
    if (mb_strlen($user_name) === 0) {
        $error[] = 'ユーザー名を入力してください。';
    }

    if (mb_strlen($password) === 0) {
        $error[] = 'パスワードを入力してください。';
    }

    if (mb_strlen($user_name) >= 1 && mb_strlen($password) >= 1 && ($user_data[0] === '0' || $user_data[1] !== $password)) {
        $error[] = 'ユーザー名あるいはパスワードが違います。';
    }

    return $error;
}

//売り切れ表示
function sold_out_check($value) {
    if ($value['stock'] === '0') {
        print '<span class="red">売り切れ</span>';
    } else {
        print '<input name="go_cart" class="cart-btn" type="submit" value="カートに入れる">';
    }
}

//管理者かチェック
function admin_check($user_name, $password, $link) {
    $admin = '';

    //文頭・文末の空白を取り除く
    $user_name = space_trim($user_name);
    $password = space_trim($password);

    //ユーザーデータセット
    $user_data = duplicate_check($user_name, $link);

    //管理者かチェック
    if ($user_data[0] === '1' && $user_data[1] === 'admin') {
        $admin = TRUE;
    } else {
        $admin = FALSE;
    }

    return $admin;
}

//ログイン画面のセッションチェック
function login_check_session() {
    session_start();
    if (isset($_SESSION['user_name']) === TRUE) {
        if ($_SESSION['user_name'] === 'admin') {
            header('location:
            https://bagged-zed-23718.herokuapp.com/admin.php');
        } else {
            header('location: https://bagged-zed-23718.herokuapp.com/top.php');
        }
    }
}

//管理画面のセッションチェック
function admin_check_session() {
    session_start();
    if (!isset($_SESSION['user_name']) === TRUE) {
        header('location: https://bagged-zed-23718.herokuapp.com');
    } else if ($_SESSION['user_name'] !== 'admin') {
        header('location: https://bagged-zed-23718.herokuapp.com/top.php');
    }
}

//セッションがない場合
function check_session() {
    session_start();
    if (!isset($_SESSION['user_name']) === TRUE) {
        header('location: https://bagged-zed-23718.herokuapp.com/');
    }
}


//セッションセット
function set_session($user_name, $user_id) {
    session_start();
    if (!isset($_SESSION['user_name']) === TRUE && !isset($_SESSION['user_id']) === TRUE) {
        $_SESSION['user_name'] = $user_name;
        $_SESSION['user_id'] = $user_id;
    }
}

//ログアウト
function logout() {
    session_start();
    unset($_SESSION['user_name']);
    unset($_SESSION['user_id']);
    header('location: https://bagged-zed-23718.herokuapp.com/');
}

//カートに入れる
function insert_cart($link, $item_id, $now_date) {
    $user_id = intval($_SESSION['user_id']);
    $sql = '';

    mysqli_set_charset($link, DB_CHARACTER_SET);
    $amount = check_cart($link, $user_id, $item_id); //カートに入れる商品が存在するか確認

    if ($amount[2] === NULL) {
        $sql = 'INSERT INTO cart(user_id, item_id, amount, created_date, updated_date) VALUES (' . $user_id . ',' . $item_id . ', 1,\'' . $now_date . '\',\'' . $now_date . '\')';
    } else if (intval($amount[2] >= 1)){
        $sql = 'UPDATE cart SET amount = amount + 1 WHERE user_id = ' . $user_id . ' AND item_id =' . $item_id;
    }

    mysqli_query($link, $sql);
}

//個数確認
function check_cart($link, $user_id, $item_id) {
    mysqli_set_charset($link, DB_CHARACTER_SET);
    $sql = 'SELECT user_id, item_id, amount FROM cart WHERE user_id =' . $user_id . ' AND item_id =' . $item_id;
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_array($result);
    mysqli_free_result($result);
    return $row; //[2]で個数
}

//カートの中身
function cart_list($link) {
    $cart_list = array();
    $user_id = intval($_SESSION['user_id']);

    mysqli_set_charset($link, DB_CHARACTER_SET);
    $sql = 'SELECT item.id, item.name, item.price, item.img, item.status, cart.user_id, cart.amount, item.status FROM item JOIN cart ON item.id = cart.item_id WHERE user_id = ' . $user_id;
    $result = mysqli_query($link, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $cart_list[] = $row;
    }
    mysqli_free_result($result);
    return $cart_list;
}

//カート削除
function cart_delete($link) {
    $item_id = $_POST['item_id'];
    $user_id = $_SESSION['user_id'];

    mysqli_set_charset($link, DB_CHARACTER_SET);
    $sql = 'DELETE FROM cart WHERE item_id =' . $item_id . ' AND user_id =' . $user_id;
    mysqli_query($link, $sql);
}

//カート個数変更のエラーチェック
function cart_err_check($select_amount) {
    $error = array();

    //空白を取り除く
    $select_amount = str_replace('　', ' ', $select_amount);
    $select_amount = trim($select_amount);

    if (mb_strlen($select_amount) === 0) {
        $error[] = '個数を入力してください。';
    } else if (preg_match('/^([1-9]\d*)$/', $select_amount) !== 1) {
        $error[] = '個数は半角数字を入力してください。';
    }

    return $error;
}

//カート個数変更
function cart_amount_change($select_amount, $link) {
    $item_id = $_POST['item_id'];
    $user_id = $_SESSION['user_id'];

    mysqli_set_charset($link, DB_CHARACTER_SET);
    $sql = 'UPDATE cart SET amount = ' . $select_amount . ' WHERE item_id =' . $item_id . ' AND user_id =' . $user_id;
    mysqli_query($link, $sql);
}

//合計金額計算
function total_price_calc($link) {
    $total_price = array(); //合計金額
    $user_id = intval($_SESSION['user_id']);

    mysqli_set_charset($link, DB_CHARACTER_SET);
    $sql = 'SELECT SUM(item.price * cart.amount) FROM item JOIN cart ON item.id = cart.item_id WHERE user_id = ' . $user_id;
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_array($result);
    mysqli_free_result($result);

    //合計金額が0円のとき
    if ($row[0] === NULL) {
        return 0;
    }

    return $row[0];
}

//購入処理
function purchase_done($link) {
    $user_id = $_SESSION['user_id'];

    mysqli_set_charset($link, DB_CHARACTER_SET);
    $sql = 'DELETE FROM cart WHERE user_id =' . $user_id;
    mysqli_query($link, $sql);
}

//個数を減らす
function cut_back_stock($link) {
    $cart_list = cart_list($link); //カートリスト取得
    $i = 0;
    $item_id = 0; //カートから受け取ったアイテムID
    $amount = 0; //カートから受け取った個数
    $stock = 0; //在庫数
    $now_date = date('Y-m-d H:i:s');
    $msg = array();

    mysqli_autocommit($link, false);
    foreach($cart_list as $value) {
        $item_id = intval($_POST['item_id_'. $i]);
        $amount = intval($_POST['select_amount_'. $i]);

        //在庫数取得
        mysqli_set_charset($link, DB_CHARACTER_SET);
        $sql = 'SELECT stock FROM stock WHERE item_id =' . $item_id;
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

            $stock = intval($row['stock']);
            if($stock >= $amount){
                $sql = 'UPDATE stock SET stock = stock - ' . $amount . ' WHERE item_id =' . $item_id;
                $sql2 = 'UPDATE stock SET updated_date = \'' . $now_date . '\' WHERE item_id =' . $item_id;
                mysqli_query($link, $sql);
                mysqli_query($link, $sql2);
                if (count($cart_list) - 1 === $i && count($msg) === 0) {
                    purchase_done($link);
                    mysqli_commit($link);
                    $msg[] = '<div class="finish-msg">ご購入ありがとうございました。</div>';
                } else {
                    $i++;
                }
            } else if ($stock < $amount) {
                $msg[] = '<p class="err">' . htmlspecialchars($value['name']) . 'の在庫がたりません。</p>';
                mysqli_rollback($link);
                if (count($cart_list) - 1 !== $i) {
                    $i++;
                }
            }
    }
    return $msg;
}
