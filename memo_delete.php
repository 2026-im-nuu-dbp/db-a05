<?php
session_start();
require "config/db.php";


// 檢查登入
if (!isset($_SESSION['user_name'])) {
    echo "我不認識你，請先登入";
    exit();
}

$user_id = $_SESSION['user_id'];


// 取得要刪除的 id（add / edit 不一樣）
if (!isset($_GET['id'])) {
    echo "缺少 ID";
    exit();
}
$id = $_GET['id'];


// 為了拿圖片 + 驗證（add 沒這段）
$stmt = $pdo->prepare("SELECT * FROM dememo WHERE id = :id");
$stmt->execute(['id' => $id]);
$data = $stmt->fetch();

if (!$data) {
    echo "找不到資料";
    exit();
}


// 防止刪別人資料
if ($data['user_id'] != $user_id) {
    echo "這不是你的資料";
    exit();
}


//刪除圖片
if ($data['image']) {
    if (file_exists("uploads/" . $data['image'])) {
        unlink("uploads/" . $data['image']);
    }
    if (file_exists("uploads/thumbs/" . $data['image'])) {
        unlink("uploads/thumbs/" . $data['image']);
    }
}


// DELETE
$stmt = $pdo->prepare("DELETE FROM dememo WHERE id = :id");
$stmt->execute(['id' => $id]);

echo "刪除成功";
?>