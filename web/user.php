<?php
//設定ファイル読み込み
require_once '..//include/conf/const.php';
require_once '..//include/model/function.php';

admin_check_session();

$user_data = array(); //ユーザー一覧配列

$link = db_connect(); //データベース接続

$user_data = get_user($link); //ユーザー一覧取得
// ユーザー管理画面表示
include_once '..//include/view/user_view.php';
