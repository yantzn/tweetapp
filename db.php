<?php
class dbconnect{

    //DB接続先
    const host = "mysql:3306";
    const user = "xrecruit3122_fp3";
    const pass = "bjcvsm79n3";
    const dbname = "xrecruit3122_recruit";

    //データベースに接続する関数
    private function pdo(){
        $dsn = sprintf("%s%s%s%s%s","mysql:dbname=", self::dbname,";","host=",self::host);
        try{
            $pdo = new PDO($dsn, self::user, self::pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        }catch(Exception $e){
          echo 'error' .$e->getMesseage;
          die();
        }
        //エラーを表示
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        return $pdo;
    }

    //INSERT,UPDATE,DELETE時の実行シングルSQL関数。
    private function single_sql_tran($sql,$obj){
       //DB接続
        $pdo=$this->pdo();
        try{
            //トランザクション開始
            $pdo->beginTransaction();
            //実行したいSQL
            $stmt=$pdo->prepare($sql);
            //パラメータにバインドする
            foreach ($obj as $bindParam){
                $stmt->bindValue($bindParam['param'], $bindParam['value'], $bindParam['type']);
            }
            //プリペアドステートメントを実行する
            $stmt->execute();
            // トランザクション完了
            $pdo->commit();
        }catch (PDOexception $e) {
            //トランザクション取り消し
            $pdo->rollBack();
            return "a";
        }
        //初期化する
        $pdo = null;
        return $stmt;
    }

   //INSERT,UPDATE,DELETE時の実行マルチSQL関数。
    private function multi_tran_exec($sql,$obj){
       //DB接続
        $pdo=$this->pdo();
        try{
            //トランザクション開始
            $pdo->beginTransaction();
            //実行したいSQL
            foreach ($sql as $preSql){
                $stmt=$pdo->prepare($preSql['state']);
                //パラメータにバインドする
                foreach ($obj as $bindParam){
                    $stmt->bindValue($bindParam['param'], $bindParam['value'], $bindParam['type']);
                }
                //プリペアドステートメントを実行する
                $stmt->execute();
            }
            // トランザクション完了
            $pdo->commit();
        }catch (PDOexception $e) {
            //トランザクション取り消し
            $pdo->rollBack();
            echo 'error' .$e->getMesseage;
            die();
        }
        //初期化する
        $pdo = null;
        return $stmt;
    }

    //SELECTの時に使用する関数。
    function select_exec($sql,$obj){
        //DB接続
        $pdo=$this->pdo();
        //実行したいSQL
        $stmt=$pdo->prepare($sql);
        //パラメータにバインドする
        foreach ($obj as $bindParam){
            $stmt->bindValue($bindParam['param'], $bindParam['value'], $bindParam['type']);
        }
        //プリペアドステートメントを実行する
        $stmt->execute();
        //全ての結果行を含む配列を取得
        $items=$stmt->fetchAll(PDO::FETCH_ASSOC);
        //初期化する
        $pdo = null;
        return $items;
    }

    //新規アカウント登録処理
     public function create_user($username,$password){
        $pdo=$this->pdo();
        try {
                //DB接続
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
                $_SESSION["NAME"] = $username;
                // トランザクション完了
                $pdo->commit();
                //初期化する
                $pdo = null;

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
    }

    //ログイン処理
    public function login_user($username,$password){
        $pdo=$this->pdo();
        try {
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
                        //初期化する
                        $pdo = null;

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
    }

    //ログインユーザとユーザがフォローしている他ユーザの投稿情報を取得する・
    function get_post_info(){
        $sql = "SELECT users.user_name, users.user_id, tweet.tweet_messages, tweet.tweet_id, tweet.tweet_created FROM users
                INNER JOIN tweet on tweet.tweet_user_id = users.user_id
                WHERE tweet_user_id = :id OR tweet.tweet_user_id IN (SELECT follower_id FROM follower WHERE user_id = :id)
                ORDER BY tweet.tweet_created DESC";
        $bindArray = array(
                      array('param' => ':id','value' => $_SESSION["ID"], 'type' => PDO::PARAM_INT),
                    );
        $res = $this->select_exec($sql,$bindArray);
        return $res;
    }

    //ログインユーザの情報を取得する
    function get_loginuser_info(){
        $sql = "SELECT * FROM
                (SELECT COUNT(*) AS tweet_cnt FROM users INNER JOIN tweet on tweet.tweet_user_id = users.user_id WHERE tweet_user_id = :id) AS tweets,
                (SELECT COUNT(*) AS follower_cnt FROM follower WHERE user_id = :id) AS folls,
                (SELECT COUNT(*) AS followered_cnt FROM followered WHERE user_id = :id) AS followers";
        $bindArray = array(
                      array('param' => ':id','value' => $_SESSION["ID"], 'type' => PDO::PARAM_INT),
                    );
        $res = $this->select_exec($sql,$bindArray);
        return $res;
    }

    //ログインユーザのフォロー情報を取得する
    function get_follower_info(){
        $sql = "SELECT * FROM users WHERE users.user_id in (SELECT follower_id FROM follower WHERE user_id = :id)";
        $bindArray = array(
                      array('param' => ':id','value' => $_SESSION["ID"], 'type' => PDO::PARAM_INT),
                    );
        $res = $this->select_exec($sql,$bindArray);
        return $res;
    }

    //ログインユーザのフォロワー情報を取得する
    function get_followered_info(){
        $sql = "SELECT * FROM users WHERE users.user_id in (SELECT followered_id FROM followered WHERE user_id = :id)";
        $bindArray = array(
                      array('param' => ':id','value' => $_SESSION["ID"], 'type' => PDO::PARAM_INT),
                    );
        $res = $this->select_exec($sql,$bindArray);
        return $res;
    }

    //他のユーザを取得する
    function get_otheruser_info(){
        $sql = "SELECT * FROM users WHERE user_id <> :id AND user_id NOT IN (SELECT follower_id FROM follower WHERE user_id = :id)";
        $bindArray = array(
                      array('param' => ':id','value' => $_SESSION["ID"], 'type' => PDO::PARAM_INT),
                    );
        $res = $this->select_exec($sql,$bindArray);
        return $res;
    }

    //投稿処理
    function tweet_post($msg){
        //投稿テーブルに追加を行う。
        $sql = "INSERT INTO tweet(tweet_user_id,tweet_messages,tweet_created) VALUES(:userid,:msg,:create_time)";
        $bindArray = array(
                    array('param' => ':userid','value' => $_SESSION["ID"], 'type' => PDO::PARAM_INT),
                    array('param' => ':msg','value' => $msg,'type' => PDO::PARAM_STR),
                    array('param' => ':create_time','value' => date('Y-m-d H:i:s'),'type' => PDO::PARAM_STR),
                  );
        $this->single_sql_tran($sql,$bindArray);
        // メイン画面へ遷移
        header("Location: main.php");
        exit();
    }

    //フォロー追加処理
    function add_follow($follo_id){
        //フォロー・フォロワーテーブルに追加を行う。
        $sql = array(
                  array('state' => 'INSERT INTO follower(user_id,follower_id) VALUES(:userid,:follower_id)'),
                  array('state' => 'INSERT INTO followered(user_id,followered_id) VALUES(:follower_id,:userid)'),
                  );
        $bindArray = array(
                    array('param' => ':userid','value' => $_SESSION["ID"], 'type' => PDO::PARAM_INT),
                    array('param' => ':follower_id','value' => $follo_id, 'type' => PDO::PARAM_INT),
                  );
        $this->multi_tran_exec($sql,$bindArray);
        // メイン画面へ遷移
        header("Location: main.php");
        exit();
    }

    //フォロー削除処理
    function remove_follow($follo_id){
        //フォロー・フォロワーテーブルの削除を行う。
        $sql= "DELETE fo,fd FROM follower AS fo INNER JOIN followered AS fd ON fd.user_id = fo.follower_id
             AND fo.user_id = fd.followered_id WHERE fo.user_id = :userid AND fo.follower_id = :follower_id";
        $bindArray = array(
                    array('param' => ':userid','value' => $_SESSION["ID"], 'type' => PDO::PARAM_INT),
                    array('param' => ':follower_id','value' => $follo_id,'type' => PDO::PARAM_INT),
                  );
        $this->single_sql_tran($sql,$bindArray);
        // メイン画面へ遷移
        header("Location: main.php");
        exit();
    }

    //ユーザ削除処理
    function delete_user(){
        $sql = array(
                      array('state' => 'DELETE FROM tweet WHERE tweet_user_id = :id'),
                      array('state' => 'DELETE FROM users WHERE user_id = :id'),
                    );
        $bindArray = array(
                      array('param' => ':id','value' => $_SESSION["ID"], 'type' => PDO::PARAM_INT),
                    );
        $this->multi_tran_exec($sql,$bindArray);
    }
}
?>