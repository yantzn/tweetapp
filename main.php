<?php
require("db.php");
require("function.php");
//セッション開始
require_logined_session();

$db = new dbconnect();
//投稿情報を取得する
$tl = $db->get_time_line($_SESSION["ID"]);
//ログインユーザの情報を取得する
$info = $db->get_login_userinfo($_SESSION["ID"]);
//フォロー情報を取得する
$follo_info = $db->get_follo_info($_SESSION["ID"]);
//フォロワー情報を取得する
$follower_info = $db->get_follower_info($_SESSION["ID"]);
//他のユーザを取得する
$anothe = $db->get_other_userinfo($_SESSION["ID"]);
//投稿ボタンがクリックされた場合
if($_POST['tweet']){
    //投稿テーブルに追加を行う。
    $db->tweet_post($_SESSION["ID"],$_POST["tweet_msg"]);
}
//フォローボタンがクリックされた場合
if($_POST['follo']){
  //フォロー・フォロワーテーブルに追加を行う。
  $db->add_follow($_SESSION["ID"],$_POST["follo_id"]);
}

//フォロー解除ボタンがクリックされた場合
if($_POST['unfollo']){
  //フォロー・フォロワーテーブルに追加を行う。
  $db->remove_follow($_SESSION["ID"],$_POST["follo_id"]);
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
        <nav class="navbar navbar-default ">
          <div class="container-fluid">
            <div class="navbar-header">
              <a class="navbar-brand" href="#">TweetApp</a>
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav_target"></button>
            </div>
              <ul class="nav navbar-nav navbar-right">
                  <li><a href="logout.php">ログアウト</a></li>
              </ul>
              <ul class="nav navbar-nav navbar-right">
                  <li><a href="delete_user.php">アカウント削除</a></li>
              </ul>
            </div>
          </div>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col-xs-8">
                    <div class="panel panel-info">
                        <div class="panel-heading strong">Tweet</div>
                          <ul class="list-group">
                            <?php if(!empty($tl)) {?>
                              <?php foreach($tl as $val ): ?>
                                  <li class="list-group-item">
                                    <div class="wrapper">
                                        <div class="name"><?php echo $val['user_name']?></div>
                                        <div class="post_time">投稿日時:<?php echo $val['tweet_created']?></div>
                                    </div>
                                    <div>
                                        <div class="panel-body text-left"><?php echo $val['tweet_messages']?></div>
                                    </div>
                                  </li>
                              <?php endforeach; ?>
                            <?php }else{?>
                                  <li class="list-group-item">ツイートが見つかりません。ツイートしてみましょう。</li>
                            <?php }?>
                          </ul>
                    </div>
                </div>
                <div class="col-xs-4 ">
                    <div class="panel panel-info">
                        <div class="panel-heading strong">ユーザ情報</div>
                        <div class="panel-body text-right">
                        <div class="wrapper">
                            <div class="panel-body text-right" style="font-size:18px;"><?php echo $_SESSION["NAME"] ?></div>
                        </div>
                        <div class="wrapper">
                            <div>
                              <div class="panel-body text-right">ツイート</div>
                                <div class="tweet_cnt"><?php echo $info['tweet_cnt']?></div>
                            </div>
                            <div>
                              <div class="panel-body text-right">フォロー</div>
                              <div class="follo_cnt"><?php echo $info['follower_cnt']?></div>
                            </div>
                            <div>
                              <div class="panel-body text-right">フォロワー</div>
                              <div class="follwer_cnt"><?php echo $info['followered_cnt']?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <form class="form-horizontal" action="" method="post">
                    <input class="form-control input-lg" type="text" id="tweet_msg" name="tweet_msg" placeholder="今なにしてる？">
                    <input class="btn btn-primary pull-right" type="submit" id="tweet" name ="tweet" value="投稿する">
                </form>
                <div class="panel panel-info" style="margin-top:50px;">
                    <div class="panel-heading strong">フォロー中のユーザ</div>
                      <ul class="list-group">
                        <?php if(!empty($follo_info)) {?>
                          <?php foreach($follo_info as $val ): ?>
                              <li class="list-group-item">
                                <form class="form-horizontal" action="" method="post">
                                    <input class="btn-xs btn-danger pull-right" type="submit" id="unfollo" name ="unfollo" value="フォロー解除">
                                    <div class="name"><?php echo $val['user_name']?></div>
                                    <input type="hidden" id="follo_id" name ="follo_id"  style="display: none;" value="<?php echo $val['user_id']?>">
                                </form>
                              </li>
                          <?php endforeach; ?>
                        <?php }else{?>
                              <li class="list-group-item">まだフォローしていません。</li>
                        <?php }?>
                      </ul>
               </div>
                <div class="panel panel-info" style="margin-top:50px;">
                    <div class="panel-heading strong">フォローされているユーザ</div>
                      <ul class="list-group">
                        <?php if(!empty($follower_info)) {?>
                          <?php foreach($follower_info as $val ): ?>
                              <li class="list-group-item"><?php echo $val['user_name']?></li>
                          <?php endforeach; ?>
                        <?php }else{?>
                              <li class="list-group-item">まだフォローされていません。</li>
                        <?php }?>
                      </ul>
               </div>
                <div class="panel panel-info" style="margin-top:50px;">
                    <div class="panel-heading strong">まだフォローしていないユーザ</div>
                      <ul class="list-group">
                        <?php if(!empty($anothe)) {?>
                          <?php foreach($anothe as $val ): ?>
                              <li class="list-group-item">
                                <form class="form-horizontal" action="" method="post">
                                    <input class="btn-xs btn-primary pull-right" type="submit" id="follo" name ="follo" value="フォロー">
                                    <div class="name"><?php echo $val['user_name']?></div>
                                    <input type="hidden" id="follo_id" name ="follo_id"  style="display: none;" value="<?php echo $val['user_id']?>">
                                </form>
                              </li>
                          <?php endforeach; ?>
                        <?php }else{?>
                              <li class="list-group-item">他のユーザが見つかりません。</li>
                        <?php }?>
                      </ul>
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