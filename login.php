<?php
require_once __DIR__ . '/config/auth.php';
if (!empty($_SESSION['user_id'])) {
  header('Location: /db-a05/index.php');
  exit;
}
$error = $_SESSION['login_error'] ?? null;
$success = $_SESSION['reg_success'] ?? false;
unset($_SESSION['login_error'], $_SESSION['reg_success']);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>登入 | DB-A05</title>
  <link rel="stylesheet" href="/db-a05/assets/style.css" />
</head>

<body>
  <div class="auth-wrap">
    <h1>登入</h1>

    <?php if ($success): ?>
      <p class="alert success">註冊成功！請登入。</p>
    <?php endif; ?>

    <?php if ($error): ?>
      <p class="alert error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="/db-a05/login_process.php" method="POST">
      <label>帳號
        <input type="text" name="account" required autofocus />
      </label>
      <label>密碼
        <input type="password" name="password" required />
      </label>
      <button type="submit">登入</button>
    </form>

    <p>還沒有帳號？<a href="/db-a05/register.php">立即註冊</a></p>
  </div>
</body>

</html>