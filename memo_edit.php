<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$user = currentUser();
$db = getDB();
$message = '';
$msgType = '';

// 取得要修改的資料 id
if (!isset($_GET['id'])) {
    header('Location: /db-a05/index.php');
    exit;
}
$id = (int) $_GET['id'];

// 先抓舊資料
$stmt = $db->prepare("SELECT * FROM dememo WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    $message = '找不到資料';
    $msgType = 'error';
}

// 防止改別人的資料
if ($data && $data['user_id'] != $user['id']) {
    $message = '這不是你的資料';
    $msgType = 'error';
    $data = null;
}

// 表單送出
if ($data && $_SERVER["REQUEST_METHOD"] == "POST") {

    $content = trim($_POST['content'] ?? '');

    if (!$content) {
        $message = '請輸入內容';
        $msgType = 'error';
    } else {
        // 預設使用舊圖片
        $imageName = $data['image'];

        // 有上傳新圖片
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $file = $_FILES['image'];
            $imageName = time() . "_" . basename($file['name']);

            $target = __DIR__ . "/uploads/" . $imageName;

            if (!move_uploaded_file($file['tmp_name'], $target)) {
                $message = '圖片上傳失敗';
                $msgType = 'error';
            } else {
                // 刪除舊圖片
                if ($data['image']) {
                    $oldPath = __DIR__ . "/uploads/" . $data['image'];
                    $oldThumb = __DIR__ . "/uploads/thumbs/" . $data['image'];
                    if (file_exists($oldPath)) unlink($oldPath);
                    if (file_exists($oldThumb)) unlink($oldThumb);
                }

                // 縮圖
                $thumbPath = __DIR__ . "/uploads/thumbs/" . $imageName;
                $type = $file['type'];

                if ($type == "image/jpeg") {
                    $src = imagecreatefromjpeg($target);
                } elseif ($type == "image/png") {
                    $src = imagecreatefrompng($target);
                } else {
                    $message = '只支援 JPG / PNG 格式';
                    $msgType = 'error';
                    unlink($target);
                    $imageName = $data['image']; // 還原舊圖
                }

                if ($msgType !== 'error' && isset($src)) {
                    $width = imagesx($src);
                    $height = imagesy($src);

                    $newWidth = 200;
                    $newHeight = intval(($height / $width) * $newWidth);

                    $tmp = imagecreatetruecolor($newWidth, $newHeight);

                    if ($type == "image/png") {
                        imagealphablending($tmp, false);
                        imagesavealpha($tmp, true);
                    }

                    imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    if ($type == "image/jpeg") {
                        imagejpeg($tmp, $thumbPath, 85);
                    } else {
                        imagepng($tmp, $thumbPath);
                    }

                    imagedestroy($src);
                    imagedestroy($tmp);
                }
            }
        }

        // UPDATE
        if ($msgType !== 'error') {
            $stmt = $db->prepare("UPDATE dememo SET content = ?, image = ? WHERE id = ?");
            $stmt->execute([$content, $imageName, $id]);

            header('Location: /db-a05/index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>編輯美食記錄 | DB-A05</title>
  <link rel="stylesheet" href="/db-a05/assets/style.css" />
</head>
<body>
<nav>
  <span>👋 <?= htmlspecialchars($user['nickname']) ?></span>
  <a href="/db-a05/index.php">← 回首頁</a>
  <a href="/db-a05/logout.php">登出</a>
</nav>
<div class="container">
  <h1>✏️ 編輯美食記錄</h1>
  <?php if ($message): ?>
    <p class="alert <?= $msgType ?>"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <?php if ($data): ?>
  <form method="POST" enctype="multipart/form-data" class="memo-form">
    <label>美食心得
      <textarea name="content" rows="6" required><?= htmlspecialchars($data['content']) ?></textarea>
    </label>

    <?php if ($data['image']): ?>
      <div class="current-image">
        <p>目前圖片：</p>
        <img src="/db-a05/uploads/thumbs/<?= htmlspecialchars($data['image']) ?>" alt="目前圖片" />
      </div>
    <?php endif; ?>

    <label>更換圖片（JPG / PNG，不選則保留原圖）
      <input type="file" name="image" accept="image/jpeg,image/png" />
    </label>
    <button type="submit">儲存修改</button>
  </form>
  <?php endif; ?>
</div>
</body>
</html>