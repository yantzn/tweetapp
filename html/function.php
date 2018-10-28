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

function require_double_transmission_chk($post_ticket){
    //  ポストされたワンタイムチケットを取得する。
    if (isset($post_ticket)){
        $ticket = $post_ticket;
    }else{
        $ticket = '';        
    }

    //  セッション変数に保存されたワンタイムチケットを取得する。
    if (isset($_SESSION['ticket'])){
        $save = $_SESSION['ticket'];
    }else{
        $save = '';        
    }

    //  セッション変数を解放し、ブラウザの戻るボタンで戻った場合に備える。
    unset($_SESSION['ticket']);

    //  ポストされたワンタイムチケットの中身が空だった場合
    if ($ticket === '') {
        //不正なリクエスト
        return false;
    }

    //POSTされたワンタイムチケットとセッション変数から取得したワンタイムチケットが同じ場合
    if ($ticket === $save) {
        //正常なリクエスト
        return true;
    }else{
        //不正なリクエスト
        return false;
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