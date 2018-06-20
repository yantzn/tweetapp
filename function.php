<?php
function require_unlogined_session () {
    // セッション開始
    session_start();
    // ログインしていれば
    if (!isset($_SESSION["ID"]) && !empty($_SESSION["NAME"])) {
        header('Location: main.php');
        exit;
    }
}

function require_logined_session() {
    // セッション開始
    session_start();
    // ログインしていなければindex.phpに遷移
    if (!isset($_SESSION["ID"]) && !empty($_SESSION["NAME"])) {
        header('Location: index.php');
        exit;
    }
}

//入力値判定
function validation($data){
    // ユーザネームのバリデーション
    if(empty($data['login_name'])) {
        $error[] = "「ユーザネーム」は必ず入力してください。";
    }

    // パスワードのバリデーション
    if(empty($data['login_password'])) {
        $error[] = "「パスワード」は必ず入力してください。";
    }

    if (!preg_match("/^[\w]+$/", $data['login_password'])) {
        $error[] = "「パスワード」は半角英数で入力してください。";
    }

    return $error;
}

?>