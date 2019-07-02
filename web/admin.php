<?php
//設定ファイル読み込み
require_once '..//include/conf/const.php';
require_once '..//include/model/function.php';

//セッションチェック
admin_check_session();

$now_date = date('Y-m-d H:i:s'); // 現在日時
$msg = ''; //商品追加メッセージ
$err_msg = array(); //エラーメッセージ配列
$item_data = array(); //商品一覧配列

// DB接続
$link = db_connect();

//「商品の登録」入力チェック
if (isset($_POST['new_name']) === TRUE && isset($_POST['new_price']) === TRUE && isset($_POST['new_stock']) === TRUE && isset($_FILES['new_img']) === TRUE && isset($_POST['new_status']) === TRUE) {
    $item_name = get_post_data('new_name'); //商品名
    $price = get_post_data('new_price'); //価格
    $stock = get_post_data('new_stock'); //在庫数
    $img = get_file_data(); // 商品画像名
    $tmp = get_file_tmp(); //画像の一時保存場所
    $status = get_post_data('new_status'); //ステータス

    //エラーチェック
    $err_msg = err_check($item_name, $price, $stock, $img, $status);

    //エラーがなければ商品追加
    if (count($err_msg) === 0) {
        insert_item($item_name, $price, $img, $status, $stock, $now_date, $link, $tmp);
        $msg = '<p class="blue">商品を追加しました。</p>';
    }
}

//在庫数変更
if (isset($_POST['update_stock']) === TRUE) {
    $update_stock = get_post_data('update_stock');
    $item_id = get_post_data('item_id');

    //エラーチェック
    $err_msg = stock_change_err_check($update_stock);

    //エラーがなければ個数変更
    if (count($err_msg) === 0) {
        stock_change($update_stock, $item_id, $link, $now_date);
        $msg = '<p class="blue">在庫を変更しました。</p>';
    }
}

//ステータス変更
if (isset($_POST['change_status']) === TRUE) {
    $now_status = get_post_data('change_status'); //現在のステータス
    $item_id = get_post_data('item_id');

    //エラーチェック
    $err_msg = change_status_err_check($now_status);

    //エラーがなければステータス変更
    if (count($err_msg) === 0) {
        change_status($now_status, $item_id, $link);
        $msg = '<p class="blue">ステータスを変更しました。</p>';
    }
}

//商品削除
if (isset($_POST['delete']) === TRUE) {
    $item_id = get_post_data('item_id');
    $now_img = get_post_data('now_img'); //画像の保存場所
    delete_item($item_id, $link, $now_img); //フォルダから画像削除
    $msg = '<p class="blue">削除成功</p>';
}

// 商品の一覧を取得
$item_data = get_item($link);

//DB切断
db_close($link);

// 商覧管理ページ表示
include_once '..//include/view/admin_view.php';
