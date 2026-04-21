<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function requireLogin(): void {
  if (empty($_SESSION['user_id'])) {
    header('Location: /db-a05/login.php');
    exit;
  }
}

function currentUser(): array {
  return [
    'id' => $_SESSION['user_id'] ?? null,
    'account' => $_SESSION['account'] ?? null,
    'nickname' => $_SESSION['nickname'] ?? null,
  ];
}
