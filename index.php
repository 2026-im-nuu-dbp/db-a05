<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$user = currentUser();
$db = getDB();
$stmt = $db->prepare('SELECT m.*, u.nickname FROM dememo m JOIN dbusers u ON m.user_id = u.id ORDER BY m.created_at DESC');
$stmt->execute();
$memos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>圖文備忘 | DB-A05</title>
  <link rel="stylesheet" href="/db-a05/assets/style.css" />
</head>
<body>
<nav>
  <span>👋 <?= htmlspecialchars($user['nickname']) ?></span>
  <a href="/db-a05/memo_add.php">＋ 新增備忘</a>
  <a href="/db-a05/log_view.php">登入紀錄</a>
  <a href="/db-a05/logout.php">登出</a>
</nav>
<div class="container">
  <h1>圖文備忘清單</h1>
  <?php if (empty($memos)): ?>
    <p>目前沒有任何備忘，<a href="/db-a05/memo_add.php">新增一筆</a>吧！</p>
  <?php else: ?>
    <div class="memo-grid">
      <?php foreach ($memos as $m): ?>
        <div class="memo-card">
          <?php if ($m['image']): ?>
            <img src="/db-a05/uploads/thumbs/<?= htmlspecialchars($m['image']) ?>" alt="備忘圖片" />
          <?php endif; ?>
          <p><?= nl2br(htmlspecialchars($m['content'])) ?></p>
          <small>👤 <?= htmlspecialchars($m['nickname']) ?> ｜ 🕒 <?= $m['created_at'] ?></small>
          <div class="memo-actions">
            <a href="/db-a05/memo_edit.php?id=<?= $m['id'] ?>">編輯</a>
            <a href="/db-a05/memo_delete.php?id=<?= $m['id'] ?>" onclick="return confirm('確定刪除？')">刪除</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
