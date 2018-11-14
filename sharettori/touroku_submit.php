<?php
  function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }


  if (($_GET["username"]!=="") && ($_GET["password"])!="") {
    $username = $_GET["username"];
    $password = $_GET["password"];

    $pdo = new PDO("sqlite:myblog.sqlite");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $st = $pdo->prepare("INSERT INTO user(username, password) VALUES(?, ?)");
    $st->execute(array($username, $password));

    $result = "登録しました。";
  }
  else {
    $result = "正しく入力されていません。";
  }
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>新規登録</title>
    <link rel="stylesheet" href="myblog_style.css">
  </head>
  <body>

    <?php
    print h($result);
    ?>

    <p class="article_link">
    <a href="login_form.php">ログインする</a>
    </p>

  </body>
</html>
