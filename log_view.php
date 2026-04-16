<?php
session_start();
require "config/db.php";

// 檢查登入
if (!isset($_SESSION['user_name'])) {
    echo "請先登入";
    exit();
}

// （加分）可以限制只有管理員看 log
// if ($_SESSION['user_role'] != 'admin') {
//     echo "沒有權限查看";
//     exit();
// }

// ✅ 讀取 log 資料
$stmt = $pdo->prepare("
    SELECT * FROM dblog 
    ORDER BY login_at DESC
");
$stmt->execute();
$logs = $stmt->fetchAll();
?>

<h2>登入紀錄 Log</h2>

<a href="index.php">回首頁</a>

<hr>

<table border = "1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>帳號</th>
        <th>登入時間</th>
        <th>結果</th>
        <th>IP</th>
    </tr>

    <?php foreach ($logs as $log) { ?>
        <tr>
            <td><?php echo $log['id']; ?></td>
            <td><?php echo $log['account']; ?></td>
            <td><?php echo $log['login_at']; ?></td>

            <!-- 成功/失敗顯示 -->
            <td>
                <?php echo $log['success'] ? "成功" : "失敗"; ?>
            </td>

            <td><?php echo $log['ip']; ?></td>
        </tr>
    <?php } ?>

</table>