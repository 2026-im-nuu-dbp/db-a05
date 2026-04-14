<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$user = currentUser();
$db = getDB();
$stmt = $db->query('SELECT * FROM dblog ORDER BY login_at DESC LIMIT 200');
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>登入紀錄 | DB-A05</title>
  <link rel="stylesheet" href="/db-a05/assets/style.css" />
</head>
<body>
<nav>
  <a href="/db-a05/index.php">← 返回清單</a>
  <a href="/db-a05/logout.php">登出</a>
</nav>
<div class="container">
  <h1>登入紀錄</h1>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>帳號</th>
        <th>時間</th>
        <th>結果</th>
        <th>IP</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($logs as $i => $log): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= htmlspecialchars($log['account']) ?></td>
          <td><?= $log['login_at'] ?></td>
          <td><?= $log['success'] ? '✅ 成功' : '❌ 失敗' ?></td>
          <td><?= htmlspecialchars($log['ip'] ?? '') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
