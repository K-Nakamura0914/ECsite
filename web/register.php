<?php
//設定ファイル読み込み
require_once '..//include/model/function.php';
require_once '..//include/conf/const.php';

$now_date = date('Y-m-d H:i:s'); // 現在日時
$err_msg = array(); //エラーメッセージ配列
$msg = ''; //成功メッセージ

$link = db_connect();

if (isset($_POST['user_name']) === TRUE && isset($_POST['password']) === TRUE) {
    $user_name = get_post_data('user_name'); //入力されたユーザー名
    $password = get_post_data('password'); //入力されたパスワード

    //エラーチェック
    $err_msg = register_err_check($user_name, $password, $link);

    //エラーがなければユーザー登録完了
    if (count($err_msg) === 0) {
        $msg = '<p class="success">アカウント作成を完了しました。</p>';
        insert_new_user($user_name, $password, $now_date, $link);
    }
}

//DB切断
db_close($link);

// ユーザー登録画面取得
include_once '..//include/view/register_view.php';
