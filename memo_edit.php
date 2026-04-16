<?php
session_start();
require "config/db.php";

// 檢查登入
if (!isset($_SESSION['user_name'])) {
    echo "我不認識你，請先登入";
    exit();
}

$user_id = $_SESSION['user_id'];


// 取得要修改的資料 id
if (!isset($_GET['id'])) {
    echo "缺少 ID";
    exit();
}
$id = $_GET['id'];


// 先抓舊資料
$stmt = $pdo->prepare("SELECT * FROM dememo WHERE id = :id");
$stmt->execute(['id' => $id]);
$data = $stmt->fetch();

if (!$data) {
    echo "找不到資料";
    exit();
}


// 防止改別人的資料
if ($data['user_id'] != $user_id) {
    echo "這不是你的資料";
    exit();
}


// 表單送出
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $content = $_POST['content'];

    // 預設使用舊圖片（add 是 null）
    $imageName = $data['image'];

    // 有上傳新圖片
    if (!empty($_FILES['image']['name'])) {

        $file = $_FILES['image'];
        $imageName = time() . "_" . $file['name'];

        $target = "uploads/" . $imageName;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            echo "上傳失敗";
            exit();
        }

        //刪除舊圖片
        if ($data['image']) {
            if (file_exists("uploads/" . $data['image'])) {
                unlink("uploads/" . $data['image']);
            }
            if (file_exists("uploads/thumbs/" . $data['image'])) {
                unlink("uploads/thumbs/" . $data['image']);
            }
        }

        // 縮圖
        $thumbPath = "uploads/thumbs/" . $imageName;
        $type = $file['type'];

        if ($type == "image/jpeg") {
            $src = imagecreatefromjpeg($target);
        } elseif ($type == "image/png") {
            $src = imagecreatefrompng($target);
        } else {
            echo "只支援 JPG / PNG";
            exit();
        }

        $width = imagesx($src);
        $height = imagesy($src);

        $newWidth = 200;
        $newHeight = ($height / $width) * 200;

        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if ($type == "image/jpeg") {
            imagejpeg($tmp, $thumbPath);
        } else {
            imagepng($tmp, $thumbPath);
        }
    }

    
  //UPDATE
    $stmt = $pdo->prepare("
        UPDATE dememo 
        SET content = :content, image = :image 
        WHERE id = :id
    ");

    $stmt->execute([
        'content' => $content,
        'image' => $imageName,
        'id' => $id
    ]);

    echo "修改成功";
}
?>

<!-- 表單 -->
<form method="POST" enctype="multipart/form-data">
    <h3>歡迎回來，<?php echo $_SESSION['user_name']; ?></h3>

    <!-- 顯示舊內容 -->
    <textarea name="content"><?php echo $data['content']; ?></textarea>
    <br><br>

    <!--顯示舊圖片 -->
    <?php if ($data['image']) { ?>
        <img src="uploads/thumbs/<?php echo $data['image']; ?>" width="150">
        <br><br>
    <?php } ?>

    <input type="file" name="image">
    <br><br>

    <input type="submit" value="修改">
</form>