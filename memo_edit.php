<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$user = currentUser();
$id = (int)($_GET['id'] ?? 0);
$db = getDB();

$stmt = $db->prepare('SELECT * FROM dememo WHERE id = ?');
$stmt->execute([$id]);
$memo = $stmt->fetch();

if (!$memo) {
  header('Location: /db-a05/index.php');
  exit;
}

$error = $_SESSION['edit_error'] ?? null;
unset($_SESSION['edit_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $content = trim($_POST['content'] ?? '');
  if (!$content) {
    $_SESSION['edit_error'] = '內容不可為空';
    header("Location: /db-a05/memo_edit.php?id=$id");
    exit;
  }

  $imageName = $memo['image'];

  if (!empty($_FILES['image']['name'])) {
    $uploadDir = __DIR__ . '/uploads/';
    $thumbDir  = __DIR__ . '/uploads/thumbs/';
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
      $_SESSION['edit_error'] = '僅支援 JPG / PNG / GIF / WEBP';
      header("Location: /db-a05/memo_edit.php?id=$id");
      exit;
    }
    $imageName = uniqid('memo_', true) . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);

    $src = imagecreatefromstring(file_get_contents($uploadDir . $imageName));
    if ($src) {
      [$w, $h] = getimagesize($uploadDir . $imageName);
      $maxW = 400;
      $ratio = $w > $maxW ? $maxW / $w : 1;
      $thumb = imagescale($src, (int)($w * $ratio), (int)($h * $ratio));
      imagewebp($thumb, $thumbDir . $imageName);
      imagedestroy($src);
      imagedestroy($thumb);
    }
  }

  $stmt = $db->prepare('UPDATE dememo SET content = ?, image = ? WHERE id = ?');
  $stmt->execute([$content, $imageName, $id]);
  header('Location: /db-a05/index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>編輯備忘 | DB-A05</title>
  <link rel="stylesheet" href="/db-a05/assets/style.css" />
</head>
<body>
<nav>
  <a href="/db-a05/index.php">← 返回清單</a>
  <a href="/db-a05/logout.php">登出</a>
</nav>
<div class="container">
  <h1>編輯備忘</h1>
  <?php if ($error): ?>
    <p class="alert error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form action="/db-a05/memo_edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
    <label>備忘內容
      <textarea name="content" rows="6" required><?= htmlspecialchars($memo['content']) ?></textarea>
    </label>
    <?php if ($memo['image']): ?>
      <p>目前圖片：<img src="/db-a05/uploads/thumbs/<?= htmlspecialchars($memo['image']) ?>" style="max-width:200px" /></p>
    <?php endif; ?>
    <label>更換圖片（選填）
      <input type="file" name="image" accept="image/*" />
    </label>
    <button type="submit">更新</button>
  </form>
</div>
</body>
</html>
