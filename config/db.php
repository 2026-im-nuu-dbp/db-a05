<?php
// 後端邏輯：連接資料庫
$host = 'localhost';
$db = 'db-a05';
$user = 'root';
$pass = ''; // XAMPP / Laragon 預設通常無密碼

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 測試連線：直接存取此檔案時顯示結果
    if (basename($_SERVER['SCRIPT_FILENAME']) === 'db.php') {
        echo "✅ 資料庫連線成功！（{$db}）";
    }

} catch (PDOException $e) {
    die("資料庫連線失敗: " . $e->getMessage());
}

/**
 * 取得 PDO 連線實例（供新版程式使用）
 */
function getDB(): PDO
{
    global $pdo;
    return $pdo;
}
?>