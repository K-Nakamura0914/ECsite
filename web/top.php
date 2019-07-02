<?php
//設定ファイル読み込み
require_once '..//include/conf/const.php';
require_once '..//include/model/function.php';

//セッションチェック
check_session();

$insert_cart = ''; //カート追加メッセージ
$now_date = date('Y-m-d H:i:s'); //現在日時

//DB接続
$link = db_connect();

if (isset($_POST['go_cart']) === TRUE) {
    $item_id = $_POST['item_id']; //アイテムID
    insert_cart($link, $item_id, $now_date);
    $insert_cart = '<p class="success">カートに追加しました。</p>';
}

// 商品の一覧を取得
$item_data = get_item($link);

db_close($link);
// 商覧一覧ページ表示
include_once '..//include/view/top_view.php';
