<?php
//設定ファイル読み込み
require_once '..//include/conf/const.php';
require_once '..//include/model/function.php';

//セッションチェック
check_session();

$cart_list = array(); //カートの中身
$err_msg = array(); //エラーメッセージ配列
$msg = ''; //成功メッセージ
$empty = ''; //カートが空のときに表示
$total_price = 0; //合計金額
$i = 0;

//DB接続
$link = db_connect();

if (isset($_POST['delete']) === TRUE) {
    cart_delete($link);
    $msg = '<p class="success">削除しました。</p>';
}

//個数変更
if (isset($_POST['change']) === TRUE) {
    $select_amount = get_post_data('select_amount');
    $item_id = get_post_data('item_id');

    //エラーチェック
    $err_msg = cart_err_check($select_amount);

    //エラーがなければ個数変更
    if (count($err_msg) === 0) {
        cart_amount_change($select_amount, $link);
        $msg = '<p class="success">個数を変更しました。</p>';
    }
}


//カート一覧取得
$cart_list = cart_list($link);

//カートが空のとき
if (count($cart_list) === 0) {
    $empty = '<p>商品はありません</p>';
}

//カート合計金額
$total_price = total_price_calc($link);

//DB切断
db_close($link);

// 商覧管理ページ表示
include_once '..//include/view/cart_view.php';
