<?php
//設定ファイル読み込み
require_once '..//include/model/function.php';
require_once '..//include/conf/const.php';

//セッションチェック
login_check_session();

//DB接続
$link = db_connect();

$user_id = ''; //ユーザーID
$user_data = array(); //ユーザーデータ配列
$err_msg = array(); //エラーメッセージ配列

if (isset($_POST['user_name']) === TRUE && isset($_POST['password']) === TRUE) {
    $user_name = get_post_data('user_name'); //入力されたユーザー名
    $password = get_post_data('password'); //入力されたパスワード

    $user_data = duplicate_check($user_name, $link);
    $user_id = $user_data[2];


    //エラーチェック
    $err_msg = login_err_check($user_name, $password, $link);

    //エラーがなければログイン完了
    if (count($err_msg) === 0) {
        //管理者かチェック
        if (admin_check($user_name, $password, $link) === TRUE) {
            //管理者の場合
            db_close($link);
            set_session($user_name, $user_id);
            header('location: http://codecamp27263.lesson8.codecamp.jp//codeshop_management.php');
            exit;
        } else {
            //一般ユーザーの場合
            db_close($link);
            set_session($user_name, $user_id);
            header('location: http://codecamp27263.lesson8.codecamp.jp//top_controller.php');
            exit;
        }
    }
}
// ログイン画面読み込み
include_once '..//include/view/login_view.php';
