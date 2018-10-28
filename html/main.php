<?php
require("function.php");
require("db.php");

//セッション開始
require_logined_session();

// DB接続
$db = new dbconnect();
//投稿情報を取得する
$tl = $db->get_post_info();
//ログインユーザの情報を取得する
$info = $db->get_loginuser_info();
//フォロー情報を取得する
$follo_info = $db->get_follower_info();
//フォロワー情報を取得する
$follower_info = $db->get_followered_info();
//他のユーザを取得する
$anothe = $db->get_otheruser_info();

//投稿ボタンがクリックされた場合
if($_POST['tweet']){
    //リロードによる二重更新判定
    //二重更新でない場合
    if(require_double_transmission_chk($_POST['ticket']) === true){
    //投稿処理
      $db->tweet_post($_POST["tweet_msg"]);
      // メイン画面へ遷移
      header("Location: main.php");
      exit();
  }
}
 
//フォローボタンがクリックされた場合
if($_POST['follo']){
    //リロードによる二重更新判定
    //二重更新でない場合
    if(require_double_transmission_chk($_POST['ticket']) === true){
      //フオロー追加処理
      $db->add_follow($_POST["follo_id"]);
      // メイン画面へ遷移
      header("Location: main.php");
      exit();
    }
}

//フォロー解除ボタンがクリックされた場合
if($_POST['unfollo']){
    //リロードによる二重更新判定
    //二重更新でない場合
    if(require_double_transmission_chk($_POST['ticket']) === true){
      //フォロー・フォロワーテーブルの削除処理
      $db->remove_follow($_POST["follo_id"]);
      // メイン画面へ遷移
      header("Location: main.php");
      exit();  
  }
}

// リロード対策のワンタイムチケットを生成する。
$_SESSION['ticket'] = md5(uniqid(rand(), true));
$ticket = htmlspecialchars($_SESSION['ticket'], ENT_QUOTES);

?>
<!DOCTYPE html>
<html lang="ja">
    <?php require("header.php");?>
    <body>
      <?php require("navbar.php");?>
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
                                        <div class="name">ユーザ名:<?php echo $val['user_name']?></div>
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
                                <div class="tweet_cnt"><?php echo $info[0]['tweet_cnt']?></div>
                            </div>
                            <div>
                              <div class="panel-body text-right">フォロー</div>
                              <div class="follo_cnt"><?php echo $info[0]['follower_cnt']?></div>
                            </div>
                            <div>
                              <div class="panel-body text-right">フォロワー</div>
                              <div class="follwer_cnt"><?php echo $info[0]['followered_cnt']?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <form class="form-horizontal" action="" method="post" >
                    <input class="form-control input-lg" type="text" id="tweet_msg" name="tweet_msg" placeholder="今なにしてる？">
                    <input class="btn btn-primary pull-right" type="submit" id="tweet" name ="tweet" value="投稿する">
                    <input type="hidden" id="follo_id" name="ticket" value="<?php echo $_SESSION['ticket']?>">
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
                                    <input type="hidden" id="follo_id" name="ticket" value="<?php echo $_SESSION['ticket']?>">
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
                    <div class="panel-heading strong">フォローしていないユーザ</div>
                      <ul class="list-group">
                        <?php if(!empty($anothe)) {?>
                          <?php foreach($anothe as $val ): ?>
                              <li class="list-group-item">
                                <form class="form-horizontal" action="" method="post">
                                    <input class="btn-xs btn-primary pull-right" type="submit" id="follo" name ="follo" value="フォロー">
                                    <div class="name"><?php echo $val['user_name']?></div>
                                    <input type="hidden" id="follo_id" name ="follo_id"  style="display: none;" value="<?php echo $val['user_id']
                                    ?>">
                                    <input type="hidden" id="follo_id" name="ticket" value="<?php echo $_SESSION['ticket']?>">
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
      <?php require("inclode.php");?>
    </body>
</html>