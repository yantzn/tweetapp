<?php
require("function.php");
//セッション開始
require_logined_session();
echo $_SESSION["NAME"];
?>