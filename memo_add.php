<?php
session_start();
require "config/db.php"; // 引入資料庫連線設定


if ($_SERVER["REQUEST_METHOD"] == "POST") { // 接收表單資料

    $user_id = $_SESSION['user_id'];
    $content = $_POST['content'] ;

    // 圖片處理
    $imageName = null; // 預設沒有圖片

    if (!empty($_FILES['image']['name'])) { // 有上傳圖片，empty() 是檢查變數是否為空，這裡檢查圖片名稱是否為空來判斷是否有上傳圖片

        $file = $_FILES['image']; // 圖片資訊陣列，包含 name, type, tmp_name, error, size 等資訊
        $imageName = time()."_".$file['name']; //圖片的新名稱，使用時間戳加上原始檔名來避免重複

      
        $target = "uploads/".$imageName; // 圖片儲存路徑
        move_uploaded_file($file['tmp_name'], $target); // 將上傳的臨時檔案移動到指定位置

        
        $thumbPath = "uploads/thumbs/". $imageName; // 縮圖儲存路徑

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            echo "上傳失敗";
            exit();
        }
        
        
        $type = $_FILES['image']['type'];

        if ($type == "image/jpeg") {
            $src = imagecreatefromjpeg($target);
        } elseif ($type == "image/png") {
            $src = imagecreatefrompng($target);
        } else {
            echo "只支援 JPG / PNG";
            exit();
        }

        $width = imagesx($src); //存放原圖寬度，imagesx() 是一個用於獲取圖像寬度的函數，$src 是圖像資源，即原圖。這行程式碼的作用是獲取原圖的寬度，以便後續進行縮放計算。
        $height = imagesy($src); //存放原圖高度，imagesy() 是一個用於獲取圖像高度的函數，$src 是圖像資源，即原圖。這行程式碼的作用是獲取原圖的高度，以便後續進行縮放計算。

        $newWidth = 200;//縮圖寬度固定200，等比例縮放高度
        $newHeight = ($height / $width) * 200; //縮圖的高度 = 原圖高度 / 原圖寬度 * 縮圖寬度

        $tmp = imagecreatetruecolor($newWidth, $newHeight); //為何要建立一個新的圖像資源？因為我們需要一個新的空白畫布來放置縮放後的圖片，這樣才能保持原圖的品質和比例。
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height); // imagecopyresampled() 是一個用於縮放圖像的函數
        // $src：來源圖像資源，即原圖。

        // 0, 0：目標圖像的起始位置（左上角）。
        // 0, 0：來源圖像的起始位置（左上角）。
    
        // $newWidth, $newHeight：目標圖像的寬度和高度，即縮圖的尺寸。
        // $width, $height：來源圖像的寬度和高度，即原圖的尺寸。

      if ($type == "image/jpeg") {
          imagejpeg($tmp, $thumbPath);
      } else {
          imagepng($tmp, $thumbPath);
      }
    }

    // 存進資料庫
    $stmt = $pdo->prepare("INSERT INTO dememo (user_id, content, image) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $content, $imageName]);

    echo "新增成功";
}
?>

<!-- 表單 -->
<form method="POST" enctype="multipart/form-data">
    <textarea name="content" placeholder="寫點美食心得..."></textarea>
    <br>
    <hr>
    <br>
    <input type="file" name="image">
    <br>
    <hr>
    <br>
    <input type="submit" value="新增">
</form>