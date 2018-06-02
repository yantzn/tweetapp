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
            // トランザクション完了
            $pdo->commit();
            //セッションに保存
            $_SESSION["ID"] = $pdo->lastinsertid();
            $_SESSION["NAME"] = $username;
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

    public function delete_user($username,$password){}
}

?>