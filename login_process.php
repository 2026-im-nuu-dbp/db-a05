<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /db-a05/login.php');
  exit;
}

$account = trim($_POST['account'] ?? '');
$password = $_POST['password'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

function loginError(string $msg, string $account, PDO $db, string $ip): void {
  // 記錄失敗 log
  $stmt = $db->prepare('INSERT INTO dblog (account, success, ip) VALUES (?, 0, ?)');
  $stmt->execute([$account, $ip]);
  $_SESSION['login_error'] = $msg;
  header('Location: /db-a05/login.php');
  exit;
}

try {
  $db = getDB();
  $stmt = $db->prepare('SELECT * FROM dbusers WHERE account = ?');
  $stmt->execute([$account]);
  $user = $stmt->fetch();

  if (!$user || !password_verify($password, $user['password'])) {
    loginError('帳號或密碼錯誤', $account, $db, $ip);
  }

  // 記錄成功 log
  $stmt = $db->prepare('INSERT INTO dblog (account, success, ip) VALUES (?, 1, ?)');
  $stmt->execute([$account, $ip]);

  // 寫入 session
  $_SESSION['user_id'] = $user['id'];
  $_SESSION['account'] = $user['account'];
  $_SESSION['nickname'] = $user['nickname'];

  header('Location: /db-a05/index.php');
  exit;
} catch (PDOException $e) {
  $_SESSION['login_error'] = '系統錯誤：' . $e->getMessage();
  header('Location: /db-a05/login.php');
  exit;
}
