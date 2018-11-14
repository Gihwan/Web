<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Login form</title>
    <link rel="stylesheet" href="myblog_style.css">
  </head>
  <body>
    <form action="login_submit.php" method="get">
      <h2>ユーザ名とパスワードを入力してください</h2>
      ユーザ名
      <input type="text" name="username"><br>
      パスワード
      <input type="password" name="passwd"><br>

      <input type="submit" onclick="location.href='login_submit.php'　"value="送信">
      </form>

      <hr>
      <input type="submit" onclick="location.href='touroku_form.php'　"value="新規登録">

  </body>
</html>
