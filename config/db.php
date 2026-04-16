<?php
// 後端邏輯：連接資料庫
$host = 'localhost';
$db   = 'db_a05';
$user = 'root';
$pass = ''; // XAMPP / Laragon 預設通常無密碼

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 測試連線（可留可刪）
    // echo "DB connected";
    
} catch (PDOException $e) {
    die("資料庫連線失敗: " . $e->getMessage());
}
?>