<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$db = getDB();

$stmt = $db->prepare('SELECT image FROM dememo WHERE id = ?');
$stmt->execute([$id]);
$memo = $stmt->fetch();

if ($memo) {
  // 刪除實體圖片
  if ($memo['image']) {
    @unlink(__DIR__ . '/uploads/' . $memo['image']);
    @unlink(__DIR__ . '/uploads/thumbs/' . $memo['image']);
  }
  $db->prepare('DELETE FROM dememo WHERE id = ?')->execute([$id]);
}

header('Location: /db-a05/index.php');
exit;
