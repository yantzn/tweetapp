<?php
require("db.php");
session_start();
$db = new dbconnect();
//ユーザアカウントを削除する
$db->delete_user($_SESSION["ID"]);

// セッションの変数のクリア
$_SESSION = array();

// セッションクリア
session_destroy();

// メイン画面へ遷移
header("Location: index.php");
exit();

?>