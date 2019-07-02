<?php
//設定ファイル読み込み
require_once '..//include/conf/const.php';
require_once '..//include/model/function.php';

//セッションチェック
check_session();

$cart_list = array(); //カートの中身
$total_price = 0; //合計金額
$msg = array();

//DB接続
$link = db_connect();

//カート合計金額
$total_price = total_price_calc($link);

//カート一覧取得
$cart_list = cart_list($link);
if (count($cart_list) === 0) {
    $msg[] = '<p>商品がありません。</p>';
} else {
    //在庫から減らす
    $msg = cut_back_stock($link);
}

//DB切断
db_close($link);

//購入完了画面表示
include_once '..//include/view/finish_view.php';
