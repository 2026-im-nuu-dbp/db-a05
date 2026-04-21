<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /db-a05/register.php');
  exit;
}

$account = trim($_POST['account'] ?? '');
$nickname = trim($_POST['nickname'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';
$gender = $_POST['gender'] ?? '';
$interests = implode(',', $_POST['interests'] ?? []);

function regError(string $msg): void
{
  $_SESSION['reg_error'] = $msg;
  header('Location: /db-a05/register.php');
  exit;
}

if (!$account || !$nickname || !$password || !$gender) {
  regError('請填寫所有必填欄位');
}

if ($password !== $passwordConfirm) {
  regError('兩次密碼不一致');
}

if (strlen($password) < 6) {
  regError('密碼至少 6 個字元');
}

try {
  $db = getDB();
  $stmt = $db->prepare('SELECT id FROM dbusers WHERE account = ?');
  $stmt->execute([$account]);
  if ($stmt->fetch()) {
    regError('帳號已被使用');
  }

  $hash = password_hash($password, PASSWORD_BCRYPT);
  $stmt = $db->prepare(
    'INSERT INTO dbusers (account, nickname, password, gender, interests) VALUES (?, ?, ?, ?, ?)'
  );
  $stmt->execute([$account, $nickname, $hash, $gender, $interests]);

  $_SESSION['reg_success'] = true;
  header('Location: /db-a05/login.php');
  exit;
} catch (PDOException $e) {
  regError('系統錯誤：' . $e->getMessage());
}
