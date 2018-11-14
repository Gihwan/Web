<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Login form</title>
    <link rel="stylesheet" href="myblog_style.css">
  </head>
  <body>
    <form action="touroku_submit.php" method="get">
      <h2>新規登録</h2>
      ユーザ名
      <input type="text" name="username"><br>
      パスワード
      <input type="password" name="password"><br>

      <input type="submit" onclick="location.href='touroku_submit.php'　"value="送信">
    </form>


  </body>
</html>
