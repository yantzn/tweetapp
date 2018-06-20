<?php
require("function.php");
require("db.php");
//DB接続
$db = new dbconnect();

//セッション開始
require_unlogined_session();

//入力値を取得
if(!empty($_POST) ) {
    foreach( $_POST as $key => $value ) {
        $data[$key] = htmlspecialchars( $value, ENT_QUOTES);
    }
}

//新規アカウント作成ボタンがクリックされた場合
if($_POST['create']){
    $error = validation($data);
    if(empty($error)) {
       //新規アカウント登録処理
       $res = $db->create_user($_POST["login_name"],$_POST["login_password"]);
        if (is_string($res) === True) {
             $error[] = $res;
        }
    }
}

//ログインボタンがクリックされた場合
if($_POST['login']){
    $error = validation($data);
    if(empty($error)) {
        //ログイン処理
        $res = $db->login_user($_POST["login_name"],$_POST["login_password"]);
        if (is_string($res) === True) {
             $error[] = $res;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>TweetAPP</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/style.css">
        <!-- BootstrapのCSS読み込み -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
        <!-- ログイン画面 -->
        <div class="container">
            <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                    <div class="col-xs-10 col-xs-offset-1 col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1">
                <div class="row">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 class="panel-title">TweetAPPをはじめよう</h3>
                            </div>
                            <div class="panel-body">
                                <form action="" method="post">
                                    <form accept-charset="UTF-8" role="form">
                                    <fieldset>
                                        <?php if(!empty($error) ): ?>
                                            <ul class="error_list">
                                            <?php foreach($error as $value ): ?>
                                                <li><?php echo $value; ?></li>
                                            <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                        <div class="form-group">
                                            <input class="form-control" placeholder="ユーザネーム" name="login_name" type="text" value="<?php if( !empty($data['login_name']) ){ echo $data['login_name']; } ?>">
                                        </div>
                                        <div class="form-group">
                                            <input class="form-control" placeholder="パスワード" name="login_password" type="password" value="<?php if( !empty($data['login_password']) ){ echo $data['login_password']; } ?>">
                                        </div>
                                        <input class="btn btn-lg btn-primary btn-block" type="submit" name = "create"value="アカウントを作成する">
                                        <input class="btn btn-lg btn-info btn-block" type="submit" name="login"value="ログイン">
                                    </fieldset>
                                    </form>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--jQuery-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <!-- BootstrapのJS読み込み -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <!-- 外部スクリプトの読み込み -->
        <script src="js/scripts.js"></script>
    </body>
</html>