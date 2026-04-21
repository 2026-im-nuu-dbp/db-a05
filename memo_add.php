<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$user = currentUser();
$db = getDB();
$message = '';
$msgType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $user['id'];
    $content = trim($_POST['content'] ?? '');

    if (!$content) {
        $message = '請輸入內容';
        $msgType = 'error';
    } else {
        // 圖片處理
        $imageName = null;

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $file = $_FILES['image'];
            $imageName = time() . "_" . basename($file['name']);

            $target = __DIR__ . "/uploads/" . $imageName;
            $thumbPath = __DIR__ . "/uploads/thumbs/" . $imageName;

            if (!move_uploaded_file($file['tmp_name'], $target)) {
                $message = '圖片上傳失敗';
                $msgType = 'error';
            } else {
                // 產生縮圖
                $type = $file['type'];

                if ($type == "image/jpeg") {
                    $src = imagecreatefromjpeg($target);
                } elseif ($type == "image/png") {
                    $src = imagecreatefrompng($target);
                } else {
                    $message = '只支援 JPG / PNG 格式';
                    $msgType = 'error';
                    unlink($target);
                    $imageName = null;
                }

                if ($imageName && isset($src)) {
                    $width = imagesx($src);
                    $height = imagesy($src);

                    $newWidth = 200;
                    $newHeight = intval(($height / $width) * $newWidth);

                    $tmp = imagecreatetruecolor($newWidth, $newHeight);

                    // PNG 透明背景保留
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

        // 存進資料庫
        if ($msgType !== 'error') {
            $stmt = $db->prepare("INSERT INTO dememo (user_id, content, image) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $content, $imageName]);

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
  <title>新增美食記錄 | DB-A05</title>
  <link rel="stylesheet" href="/db-a05/assets/style.css" />
</head>
<body>
<nav>
  <span>👋 <?= htmlspecialchars($user['nickname']) ?></span>
  <a href="/db-a05/index.php">← 回首頁</a>
  <a href="/db-a05/logout.php">登出</a>
</nav>
<div class="container">
  <h1>🍜 新增美食記錄</h1>
  <?php if ($message): ?>
    <p class="alert <?= $msgType ?>"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>
  <form method="POST" enctype="multipart/form-data" class="memo-form">
    <label>美食心得
      <textarea name="content" rows="6" placeholder="寫點美食心得，像是店名、地址、推薦餐點..." required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
    </label>
    <label>上傳圖片（JPG / PNG）
      <input type="file" name="image" accept="image/jpeg,image/png" />
    </label>
    <button type="submit">新增記錄</button>
  </form>
</div>
</body>
</html>