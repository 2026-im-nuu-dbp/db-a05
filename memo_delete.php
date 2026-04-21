<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$user = currentUser();
$db = getDB();

// 取得要刪除的 id
if (!isset($_GET['id'])) {
    header('Location: /db-a05/index.php');
    exit;
}
$id = (int) $_GET['id'];

// 為了拿圖片 + 驗證
$stmt = $db->prepare("SELECT * FROM dememo WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    header('Location: /db-a05/index.php');
    exit;
}

// 防止刪別人資料
if ($data['user_id'] != $user['id']) {
    header('Location: /db-a05/index.php');
    exit;
}

// 刪除圖片檔案
if ($data['image']) {
    $origPath = __DIR__ . "/uploads/" . $data['image'];
    $thumbPath = __DIR__ . "/uploads/thumbs/" . $data['image'];
    if (file_exists($origPath)) unlink($origPath);
    if (file_exists($thumbPath)) unlink($thumbPath);
}

// DELETE
$stmt = $db->prepare("DELETE FROM dememo WHERE id = ?");
$stmt->execute([$id]);

header('Location: /db-a05/index.php');
exit;
?>