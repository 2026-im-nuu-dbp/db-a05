<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$user = currentUser();
$error = $_SESSION['memo_error'] ?? null;
unset($_SESSION['memo_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $content = trim($_POST['content'] ?? '');
  if (!$content) {
    $_SESSION['memo_error'] = '備忘內容不可為空';
    header('Location: /db-a05/memo_add.php');
    exit;
  }

  $imageName = null;

  // 處理上傳圖片
  if (!empty($_FILES['image']['name'])) {
    $uploadDir = __DIR__ . '/uploads/';
    $thumbDir  = __DIR__ . '/uploads/thumbs/';
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed)) {
      $_SESSION['memo_error'] = '僅支援 JPG / PNG / GIF / WEBP';
      header('Location: /db-a05/memo_add.php');
      exit;
    }

    $imageName = uniqid('memo_', true) . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);

    // 產生縮圖 (最大 400px 寬)
    $src = imagecreatefromstring(file_get_contents($uploadDir . $imageName));
    if ($src) {
      [$w, $h] = getimagesize($uploadDir . $imageName);
      $maxW = 400;
      $ratio = $w > $maxW ? $maxW / $w : 1;
      $newW = (int)($w * $ratio);
      $newH = (int)($h * $ratio);
      $thumb = imagescale($src, $newW, $newH);
      imagewebp($thumb, $thumbDir . $imageName);
      imagedestroy($src);
      imagedestroy($thumb);
    }
  }

  $db = getDB();
  $stmt = $db->prepare('INSERT INTO dememo (user_id, content, image) VALUES (?, ?, ?)');
  $stmt->execute([$user['id'], $content, $imageName]);
  header('Location: /db-a05/index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>新增備忘 | DB-A05</title>
  <link rel="stylesheet" href="/db-a05/assets/style.css" />
</head>
<body>
<nav>
  <a href="/db-a05/index.php">← 返回清單</a>
  <a href="/db-a05/logout.php">登出</a>
</nav>
<div class="container">
  <h1>新增圖文備忘</h1>
  <?php if ($error): ?>
    <p class="alert error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form action="/db-a05/memo_add.php" method="POST" enctype="multipart/form-data">
    <label>備忘內容
      <textarea name="content" rows="6" required></textarea>
    </label>
    <label>上傳圖片（選填）
      <input type="file" name="image" accept="image/*" />
    </label>
    <button type="submit">儲存</button>
  </form>
</div>
</body>
</html>
