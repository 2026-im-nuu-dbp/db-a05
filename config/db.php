<?php

$host = 'localhost';
$db = 'db-a05';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


    if (basename($_SERVER['SCRIPT_FILENAME']) === 'db.php') {
        echo "✅ 資料庫連線成功！（{$db}）";
    }

} catch (PDOException $e) {
    die("資料庫連線失敗: " . $e->getMessage());
}

function getDB(): PDO
{
    global $pdo;
    return $pdo;
}
?>