<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$user = currentUser();
$db = getDB();

// 讀取 log 資料
$stmt = $db->prepare("SELECT * FROM dblog ORDER BY login_at DESC");
$stmt->execute();
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
  <span>👋 <?= htmlspecialchars($user['nickname']) ?></span>
  <a href="/db-a05/index.php">← 回首頁</a>
  <a href="/db-a05/logout.php">登出</a>
</nav>
<div class="container">
  <h1>📋 登入紀錄</h1>

  <?php if (empty($logs)): ?>
    <p>目前沒有任何登入紀錄。</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>帳號</th>
          <th>登入時間</th>
          <th>結果</th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $log): ?>
          <tr>
            <td><?= $log['id'] ?></td>
            <td><?= htmlspecialchars($log['account']) ?></td>
            <td><?= htmlspecialchars($log['login_at']) ?></td>
            <td>
              <?php if ($log['success']): ?>
                <span class="badge success">✅ 成功</span>
              <?php else: ?>
                <span class="badge error">❌ 失敗</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($log['ip']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
</body>
</html>