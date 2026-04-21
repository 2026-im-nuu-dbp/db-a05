<?php
require_once __DIR__ . '/config/auth.php';
if (!empty($_SESSION['user_id'])) {
  header('Location: /db-a05/index.php');
  exit;
}
$error = $_SESSION['reg_error'] ?? null;
unset($_SESSION['reg_error']);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>註冊 | DB-A05</title>
  <link rel="stylesheet" href="/db-a05/assets/style.css" />
</head>

<body>
  <div class="auth-wrap">
    <h1>建立帳號</h1>

    <?php if ($error): ?>
      <p class="alert error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="/db-a05/register_process.php" method="POST">
      <label>帳號
        <input type="text" name="account" required maxlength="50" />
      </label>
      <label>暱稱
        <input type="text" name="nickname" required maxlength="50" />
      </label>
      <label>密碼
        <input type="password" name="password" required minlength="6" />
      </label>
      <label>確認密碼
        <input type="password" name="password_confirm" required minlength="6" />
      </label>
      <fieldset>
        <legend>性別</legend>
        <label><input type="radio" name="gender" value="male" required /> 男</label>
        <label><input type="radio" name="gender" value="female" /> 女</label>
        <label><input type="radio" name="gender" value="other" /> 其他</label>
      </fieldset>
      <fieldset>
        <legend>興趣（可複選）</legend>
        <label><input type="checkbox" name="interests[]" value="reading" /> 閱讀</label>
        <label><input type="checkbox" name="interests[]" value="gaming" /> 遊戲</label>
        <label><input type="checkbox" name="interests[]" value="music" /> 音樂</label>
        <label><input type="checkbox" name="interests[]" value="sports" /> 運動</label>
        <label><input type="checkbox" name="interests[]" value="travel" /> 旅遊</label>
      </fieldset>
      <button type="submit">註冊</button>
    </form>

    <p>已有帳號？<a href="/db-a05/login.php">登入</a></p>
  </div>
</body>

</html>