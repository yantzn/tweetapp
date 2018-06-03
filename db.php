<?php
class dbconnect{

    //DB接続先
    const host = "mysql:3306";
    //const host = "mysql3013.xserver.jp";
    const user = "xrecruit3122_fp3";
    const pass = "bjcvsm79n3";
    const dbname = "xrecruit3122_recruit";

    //新規アカウント登録処理
     public function create_user($username,$password){
        try {
            //DB接続
            $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
            $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            //会員テーブル
            $stmt = $pdo->prepare("INSERT INTO users(user_name,user_password,user_created) VALUES(:username,:password,:create_time)");
            // トランザクション開始
            $pdo->beginTransaction();
            //bindValueメソッドでパラメータをセット
            $stmt->bindValue(':username',$username,PDO::PARAM_STR);
            $stmt->bindValue(':password',password_hash($password, PASSWORD_DEFAULT));
            $stmt->bindValue(':create_time', date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $executed = $stmt->execute();
            //セッションに保存
            $_SESSION["ID"] = $pdo->lastinsertid('user_id');
            var_dump($_SESSION["ID"]);
            $_SESSION["NAME"] = $username;
            // トランザクション完了
            $pdo->commit();
            // メイン画面へ遷移
            header("Location: main.php");
            exit();
        } catch (PDOexception $e) {
            // トランザクション取り消し
            $pdo->rollBack();
            //ニックネームの重複エラー
            if ($e->getCode() == '23000') {
                $msg = "そのニックネームは登録済みのため登録できません。";
                return $msg;
            }
            die();
        }
        //初期化する
        $pdo = null;
        return true;
    }

    //ログイン処理
    public function login_user($username,$password){
        try {
                //DB接続
                $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
                $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                //会員テーブル
                $stmt = $pdo->prepare("SELECT * FROM users WHERE user_name = :username");
                //bindValueメソッドでパラメータをセット
                $stmt->bindValue(':username',$username,PDO::PARAM_STR);
                //クエリの実行
                $stmt->execute();
                if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (password_verify($password, $row['user_password'])) {
                        session_regenerate_id(true);
                        //セッションに保存
                        $_SESSION["ID"] = $row['user_id'];
                        $_SESSION["NAME"] = $row['user_name'];
                        // メイン画面へ遷移
                        header("Location: main.php");
                        exit();
                    } else {
                        // 認証失敗
                        $msg = 'ユーザ名またはパスワードが間違っています。';
                        return $msg;
                    }
                } else {
                    // 該当データなし
                    $msg = '該当アカウントが見つかりません。';
                    return $msg;
                }

            } catch (PDOexception $e) {
                $msg = 'DB接続に失敗しました';
                //初期化する
                $pdo = null;
                die();
            }
            //初期化する
            $pdo = null;
            //処理終了
            return true;
    }

    //投稿処理
    public function tweet_post($userid,$msg){
        try {
            //DB接続
            $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
            $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            //会員テーブル
            $stmt = $pdo->prepare("INSERT INTO tweet(tweet_user_id,tweet_messages,tweet_created) VALUES(:userid,:msg,:create_time)");
            // トランザクション開始
            $pdo->beginTransaction();
            //bindValueメソッドでパラメータをセット
            $stmt->bindValue(':userid',$userid,PDO::PARAM_INT);
            $stmt->bindValue(':msg',$msg,PDO::PARAM_STR);
            $stmt->bindValue(':create_time', date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $executed = $stmt->execute();
            // トランザクション完了
            $pdo->commit();
            // メイン画面へ遷移
            header("Location: main.php");
            exit();
        } catch (PDOexception $e) {
            // トランザクション取り消し
            $pdo->rollBack();
            die();
        }
        //初期化する
        $pdo = null;
        return true;
    }

    //指定ユーザのタイムライン表示処理
    public function get_time_line($userid){
        try {
                //DB接続
                $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
                $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                //投稿テーブル
                $stmt = $pdo->prepare("SELECT * FROM users INNER JOIN tweet on tweet.tweet_user_id = users.user_id WHERE tweet_user_id = :id");
                //bindValueメソッドでパラメータをセット
                $stmt->bindValue(':id',$userid,PDO::PARAM_INT);
                //クエリの実行
                $stmt->execute();
                //取得結果の格納
                while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                    $res[] = $row;
                }
            } catch (PDOexception $e) {
                //初期化する
                $pdo = null;
                die();
            }
            //初期化する
            $pdo = null;
            //処理終了
            return $res;
    }

    //ログインユーザの投稿数、フォロー数、フォロワー数取得処理
    public function get_login_userinfo($userid){
        try {
                //結果格納用変数
                $res = array();
                //DB接続
                $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
                $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                //投稿テーブル
                $stmt = $pdo->prepare("SELECT COUNT(tweet_user_id) AS tweet_cnt FROM users INNER JOIN tweet on tweet.tweet_user_id = users.user_id WHERE tweet_user_id = :id");
                //bindValueメソッドでパラメータをセット
                $stmt->bindValue(':id',$userid,PDO::PARAM_INT);
                //クエリの実行
                $stmt->execute();
                $tweet_cnt = $stmt->fetch();
                $res['tweet_cnt'] = $tweet_cnt['tweet_cnt'];

                //フォローテーブル
                $stmt = $pdo->prepare("SELECT COUNT(*) AS follower_cnt FROM follower WHERE user_id = :id");
                //bindValueメソッドでパラメータをセット
                $stmt->bindValue(':id',$userid,PDO::PARAM_INT);
                //クエリの実行
                $stmt->execute();
                //結果を表示
                $follower_cnt = $stmt->fetch();
                $res['follower_cnt'] = $follower_cnt['follower_cnt'];

                //フォローテーブル
                $stmt = $pdo->prepare("SELECT COUNT(*) AS followered_cnt FROM followered WHERE user_id = :id");
                //bindValueメソッドでパラメータをセット
                $stmt->bindValue(':id',$userid,PDO::PARAM_INT);
                //クエリの実行
                $stmt->execute();
                //結果を表示
                $followered_cnt = $stmt->fetch();
                $res['followered_cnt'] = $followered_cnt['followered_cnt'];

            } catch (PDOexception $e) {
                //初期化する
                $pdo = null;
                die();
            }
            //初期化する
            $pdo = null;
            //処理終了
            return $res;
    }

    //ユーザアカウント削除処理
    public function delete_user($userid){
        try {
                //結果格納用変数
                $res = array();
                //DB接続
                $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
                $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                //投稿テーブル
                $stmt = $pdo->prepare("DELETE FROM tweet WHERE tweet_user_id = :id");
                //bindValueメソッドでパラメータをセット
                $stmt->bindValue(':id',$userid,PDO::PARAM_INT);
                //クエリの実行
                $stmt->execute();

                //会員テーブル
                $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :id");
                //bindValueメソッドでパラメータをセット
                $stmt->bindValue(':id',$userid,PDO::PARAM_INT);
                //クエリの実行
                $stmt->execute();

        } catch (PDOexception $e) {
            //初期化する
            $pdo = null;
            die();
        }

        //初期化する
        $pdo = null;
        //処理終了
        return true;
    }

    //他のユーザを取得する
    public function get_other_userinfo($userid){
        try {
                //結果格納用変数
                $res = array();
                //DB接続
                $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
                $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                //会員テーブル
                $stmt = $pdo->prepare("SELECT * FROM users WHERE  user_id <> :id");
                //bindValueメソッドでパラメータをセット
                $stmt->bindValue(':id',$userid,PDO::PARAM_INT);
                //クエリの実行
                $stmt->execute();
                //取得結果の格納
                while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                    $res[] = $row;
                }

            } catch (PDOexception $e) {
                //初期化する
                $pdo = null;
                die();
            }
            //初期化する
            $pdo = null;
            //処理終了
            return $res;
    }

    //フォロー追加処理
    public function add_follow($userid,$followerid){
        try {
            //DB接続
            $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
            $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            //フォローテーブル
            $stmt = $pdo->prepare("INSERT INTO follower(user_id,follower_id) VALUES(:userid,:follower_id)");
            // トランザクション開始
            $pdo->beginTransaction();
            //bindValueメソッドでパラメータをセット
            $stmt->bindValue(':userid',$userid,PDO::PARAM_INT);
            $stmt->bindValue(':follower_id',$followerid,PDO::PARAM_INT);
            $executed = $stmt->execute();
            // トランザクション完了
            $pdo->commit();

            //フォロワーテーブル
            $stmt = $pdo->prepare("INSERT INTO followered(user_id,followered_id) VALUES(:follower_id,:userid)");
            // トランザクション開始
            $pdo->beginTransaction();
            //bindValueメソッドでパラメータをセット
            $stmt->bindValue(':userid',$userid,PDO::PARAM_INT);
            $stmt->bindValue(':follower_id',$followerid,PDO::PARAM_INT);
            $executed = $stmt->execute();
            // トランザクション完了
            $pdo->commit();

            // メイン画面へ遷移
            header("Location: main.php");
            exit();
        } catch (PDOexception $e) {
            // トランザクション取り消し
            $pdo->rollBack();
            die();
        }
        //初期化する
        $pdo = null;
        return true;
    }

    //フォロー削除処理
    public function remove_follow($userid,$followerid){
        try {
            //DB接続
            $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
            $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            //フォローテーブル
            $stmt = $pdo->prepare("DELETE FROM follower WHERE user_id = :userid AND follower_id = :follower_id");
            // トランザクション開始
            $pdo->beginTransaction();
            //bindValueメソッドでパラメータをセット
            $stmt->bindValue(':userid',$userid,PDO::PARAM_INT);
            $stmt->bindValue(':follower_id',$followerid,PDO::PARAM_INT);
            $executed = $stmt->execute();
            // トランザクション完了
            $pdo->commit();

            //フォロワーテーブル
            $stmt = $pdo->prepare("DELETE FROM followered WHERE user_id = :follower_id AND followered_id = :userid");
            // トランザクション開始
            $pdo->beginTransaction();
            //bindValueメソッドでパラメータをセット
            $stmt->bindValue(':userid',$userid,PDO::PARAM_INT);
            $stmt->bindValue(':follower_id',$followerid,PDO::PARAM_INT);
            $executed = $stmt->execute();
            // トランザクション完了
            $pdo->commit();

            // メイン画面へ遷移
            header("Location: main.php");
            exit();
        } catch (PDOexception $e) {
            // トランザクション取り消し
            $pdo->rollBack();
            die();
        }
        //初期化する
        $pdo = null;
        return true;
    }

    //フォローしているユーザを取得する
    public function get_follo_info($userid){
        try {
                //結果格納用変数
                $follower = array();
                //DB接続
                $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
                $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                //フォローテーブル
                $stmt = $pdo->prepare("SELECT follower_id FROM follower WHERE user_id = :id");
                //bindValueメソッドでパラメータをセット
                $stmt->bindValue(':id',$userid,PDO::PARAM_INT);
                //クエリの実行
                $stmt->execute();
                //結果を表示
                while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                    $follower[] = $row;
                }

                foreach ($follower as $val) {
                    //会員テーブル
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE  users.user_id = :id");
                    //bindValueメソッドでパラメータをセット
                    $stmt->bindValue(':id',$val['follower_id'],PDO::PARAM_INT);
                    //クエリの実行
                    $stmt->execute();
                    //取得結果の格納
                    while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                        $res[] = $row;
                    }
                }

            } catch (PDOexception $e) {
                //初期化する
                $pdo = null;
                die();
            }
            //初期化する
            $pdo = null;
            //処理終了
            return $res;
    }

   //フォローされているユーザを取得する
    public function get_follower_info($userid){
        try {
                //結果格納用変数
               $followered = array();
                //DB接続
                $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
                $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                //フォローテーブル
                $stmt = $pdo->prepare("SELECT followered_id FROM followered WHERE user_id = :id");
                //bindValueメソッドでパラメータをセット
                $stmt->bindValue(':id',$userid,PDO::PARAM_INT);
                //クエリの実行
                $stmt->execute();
                //取得結果の格納
                while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                    //会員テーブル
                    $stmt = $pdo->prepare("SELECT user_id,user_name FROM users WHERE user_id = :id");
                    $stmt->bindValue(':id',$row['followered_id'],PDO::PARAM_INT);
                    //クエリの実行
                    $stmt->execute();
                    //結果を表示
                    while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                        $res[] = $row;
                    }
                }
            } catch (PDOexception $e) {
                //初期化する
                $pdo = null;
                die();
            }
            //初期化する
            $pdo = null;
            //処理終了
            return $res;
    }
}
?>