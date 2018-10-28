<?php
session_start();

// セッションの変数のクリア
$_SESSION = array();

// セッションクリア
session_destroy();

// ログイン画面へ遷移
header("Location: index.php");
exit();

?>
