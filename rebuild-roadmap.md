# 從零復現「個人私藏美食地圖系統」— 完整學習路線圖

## 專案概覽

| 層級 | 技術 |
|------|------|
| 後端 | PHP 8（原生程序式，無框架） |
| 資料庫 | MySQL / MariaDB + PDO |
| 前端 | HTML5 + CSS3（無 JavaScript） |
| 認證 | bcrypt 密碼雜湊 + PHP Session |
| 圖片處理 | GD Library |
| 開發環境 | Laragon（Apache + MySQL + PHP） |

---

## 第一階段：環境建置與基礎知識（預估 1～2 天）

### 要做的事
- 安裝 Laragon（或 XAMPP），確保 Apache、MySQL、PHP 三者能正常運作
- 在瀏覽器打開 `localhost`，確認看到歡迎頁面
- 用 phpMyAdmin 或命令列建立一個測試資料庫，確認連線正常

### 需要學習的技術與細節
1. **Laragon / XAMPP 安裝與設定**
   - 了解 Apache 是什麼（Web 伺服器，負責接收 HTTP 請求並回傳檔案）
   - 了解 MySQL/MariaDB 是什麼（關聯式資料庫）
   - 了解 PHP 如何被 Apache 解析（Apache 收到 .php 請求 → 交給 PHP 引擎執行 → 回傳 HTML）
   - DocumentRoot 的概念：為什麼把檔案放在 `www/` 資料夾就能在瀏覽器打開

2. **基本 PHP 語法（先摸過一遍即可）**
   - 變數宣告（`$variable`）、字串、陣列
   - `echo` 輸出
   - `if / else`、`foreach` 迴圈
   - `include` / `require` 引入其他檔案
   - 超全域變數概念：`$_GET`、`$_POST`、`$_SESSION`、`$_FILES`、`$_SERVER`

3. **phpMyAdmin 基本操作**
   - 建立資料庫、建立資料表
   - 手動新增 / 查詢資料

---

## 第二階段：資料庫設計（預估 1 天）

### 要做的事
- 設計三張資料表的結構（用戶表、登入記錄表、美食筆記表）
- 撰寫 SQL DDL 語句，在 MySQL 中建立資料表
- 用 phpMyAdmin 手動插入測試資料，確認結構正確

### 需要學習的技術與細節
1. **SQL 基礎語法**
   - `CREATE TABLE` — 建立資料表
   - 欄位型別：`INT`、`VARCHAR(n)`、`TEXT`、`DATETIME`、`ENUM`、`TINYINT`
   - 約束條件：`PRIMARY KEY`、`NOT NULL`、`UNIQUE`、`DEFAULT`、`AUTO_INCREMENT`
   - `UNSIGNED` 的意義（只允許正整數，範圍加倍）

2. **資料表關聯設計**
   - 外鍵（Foreign Key）的概念與語法
   - `ON DELETE CASCADE` 的含義：當父記錄被刪除時，子記錄自動跟著刪除
   - 一對多關係：一個用戶可以有多筆美食筆記

3. **資料庫設計原則**
   - 每張表需要一個自動遞增的主鍵 `id`
   - 時間戳欄位：`created_at` 搭配 `DEFAULT CURRENT_TIMESTAMP`
   - `updated_at` 搭配 `ON UPDATE CURRENT_TIMESTAMP` 自動記錄更新時間

4. **三張表的具體結構**

   **dbusers**（用戶表）：id, account(唯一), nickname, password(雜湊後255字元), gender(ENUM), interests

   **dblog**（登入記錄表）：id, account, login_at, success(0/1), ip

   **dbmemo**（美食筆記表）：id, user_id(FK→dbusers.id), content, image, created_at, updated_at

---

## 第三階段：PHP 連線資料庫（預估 0.5 天）

### 要做的事
- 建立 `config/db.php`，用 PDO 連線 MySQL
- 寫一個簡單的測試頁面，從資料庫讀取資料並顯示

### 需要學習的技術與細節
1. **PDO（PHP Data Objects）**
   - 什麼是 PDO：PHP 提供的資料庫抽象層，支援多種資料庫
   - DSN 連線字串格式：`mysql:host=localhost;dbname=xxx;charset=utf8mb4`
   - 建立 PDO 實例：`new PDO($dsn, $user, $password, $options)`
   - 重要選項設定：
     - `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`（錯誤時拋出例外）
     - `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`（回傳關聯陣列）

2. **字元編碼**
   - 為什麼用 `utf8mb4` 而不是 `utf8`：utf8mb4 支援完整 Unicode，包含 emoji
   - 確保資料庫、連線、HTML 頁面三者編碼一致

3. **函式封裝**
   - 把連線邏輯包在函式裡（如 `getDB()`），方便全站重複使用
   - `require_once` 避免重複引入

---

## 第四階段：用戶註冊功能（預估 1～2 天）

### 要做的事
- 建立 `register.php`（註冊表單頁面）
- 建立 `register_process.php`（處理表單送出的邏輯）
- 完成：帳號、暱稱、密碼、性別、興趣的輸入與儲存

### 需要學習的技術與細節
1. **HTML 表單基礎**
   - `<form method="POST" action="register_process.php">`
   - 表單元素：`<input type="text">`, `<input type="password">`, `<input type="radio">`, `<input type="checkbox">`, `<select>`
   - `name` 屬性的重要性：決定了 `$_POST['xxx']` 的鍵名
   - `required` 屬性：HTML5 原生驗證

2. **PHP 接收表單資料**
   - `$_POST` 超全域變數：接收 POST 方法送來的資料
   - `trim()` 清除前後空白
   - `empty()` 檢查是否為空值

3. **密碼安全處理（非常重要）**
   - **絕對不能明文儲存密碼**
   - `password_hash($password, PASSWORD_BCRYPT)`：產生 bcrypt 雜湊值（自動加鹽）
   - 雜湊後的字串長度約 60 字元，但欄位設 255 以備未來演算法升級
   - bcrypt 的原理概念：單向雜湊 + 隨機鹽值 + 成本因子

4. **PDO 預處理語句（Prepared Statement）**
   - 為什麼需要：防止 SQL Injection 攻擊
   - 語法：`$stmt = $db->prepare("INSERT INTO dbusers (account, ...) VALUES (?, ?)")`
   - 綁定參數：`$stmt->execute([$account, $nickname, ...])`
   - 永遠不要直接把使用者輸入拼接進 SQL 字串

5. **伺服器端驗證**
   - 檢查必填欄位是否為空
   - 檢查兩次密碼輸入是否一致
   - 檢查密碼最小長度（≥ 6 字元）
   - 檢查帳號是否已被註冊（先 SELECT 查詢）

6. **頁面跳轉**
   - `header("Location: login.php")` — HTTP 重導向
   - 重導向前不能有任何輸出（echo、HTML）
   - `exit` 確保重導向後不再執行後續程式碼

---

## 第五階段：用戶登入與 Session 管理（預估 1～2 天）

### 要做的事
- 建立 `login.php`（登入表單）
- 建立 `login_process.php`（驗證帳密、建立 Session、記錄登入日誌）
- 建立 `config/auth.php`（Session 輔助函式）
- 建立 `logout.php`（登出）

### 需要學習的技術與細節
1. **Session 機制（核心概念）**
   - HTTP 是無狀態協議：伺服器不記得上一次請求是誰
   - Session 的原理：伺服器產生唯一 Session ID → 存在 Cookie 裡送給瀏覽器 → 每次請求帶回來
   - `session_start()`：每個需要使用 Session 的頁面最頂端都要呼叫
   - 寫入：`$_SESSION['user_id'] = $row['id']`
   - 讀取：`$_SESSION['user_id']`
   - 銷毀：`session_destroy()`

2. **密碼驗證**
   - `password_verify($inputPassword, $hashedPassword)`：比對使用者輸入與資料庫中的雜湊值
   - 注意：不是再做一次 hash 然後比較字串，因為每次 hash 的鹽值不同

3. **登入日誌記錄**
   - 不論登入成功或失敗，都寫一筆到 `dblog`
   - 記錄 IP 位址：`$_SERVER['REMOTE_ADDR']`
   - 記錄成功/失敗旗標：`success = 1` 或 `0`

4. **存取控制函式**
   - `requireLogin()`：檢查 `$_SESSION['user_id']` 是否存在，不存在就跳轉到登入頁
   - `currentUser()`：回傳當前登入者的資訊陣列
   - 每個需要登入才能看的頁面，在最頂端 `require_once 'config/auth.php'` 並呼叫 `requireLogin()`

5. **登出流程**
   - `session_start()` → `session_destroy()` → `header("Location: login.php")`

---

## 第六階段：美食筆記 CRUD — 列表與新增（預估 1～2 天）

### 要做的事
- 建立 `index.php`（首頁，列出所有美食筆記卡片）
- 建立 `memo_add.php`（新增筆記表單 + 處理邏輯寫在同一頁）
- 實現圖片上傳與縮圖產生

### 需要學習的技術與細節
1. **PDO 查詢與迴圈輸出**
   - `$stmt = $db->query("SELECT ... FROM dbmemo ORDER BY created_at DESC")`
   - `$stmt->fetchAll()` 取得所有結果
   - `foreach ($memos as $memo)` 迴圈產生 HTML 卡片

2. **PHP 混合 HTML 的寫法**
   - `<?php foreach (...): ?>` ... `<?php endforeach; ?>` 替代語法
   - 在 HTML 中嵌入 PHP 變數：`<?= htmlspecialchars($memo['content']) ?>`

3. **XSS 防護**
   - `htmlspecialchars($string, ENT_QUOTES, 'UTF-8')`
   - 每次輸出使用者資料到 HTML 時都要使用
   - 原理：把 `<`, `>`, `"`, `'` 等字元轉換為 HTML 實體，防止惡意腳本注入

4. **檔案上傳（重要）**
   - 表單必須加 `enctype="multipart/form-data"`
   - `$_FILES['image']`：包含 `name`, `tmp_name`, `type`, `error`, `size`
   - 檔案驗證：
     - 檢查 `$_FILES['image']['error'] === UPLOAD_ERR_OK`
     - 用 `mime_content_type()` 檢查 MIME 類型（只允許 image/jpeg, image/png）
   - 檔案命名：`time() . '_' . basename($originalName)` 避免覆蓋
   - `move_uploaded_file($tmp_name, $targetPath)`：把暫存檔移到目的地

5. **GD Library 圖片處理**
   - `getimagesize()` 取得原圖寬高
   - `imagecreatefromjpeg()` / `imagecreatefrompng()` 讀取原圖
   - `imagecreatetruecolor($newWidth, $newHeight)` 建立空白畫布
   - `imagecopyresampled()` 等比例縮放複製
   - `imagejpeg($thumb, $path, 85)` / `imagepng($thumb, $path)` 儲存縮圖
   - PNG 透明背景處理：`imagealphablending()` + `imagesavealpha()`
   - `imagedestroy()` 釋放記憶體
   - 縮圖邏輯：固定寬度 200px，高度按比例計算 `$newHeight = ($origHeight / $origWidth) * 200`

6. **目錄結構**
   - `uploads/` 放原圖
   - `uploads/thumbs/` 放縮圖
   - 確保這兩個目錄存在且有寫入權限

---

## 第七階段：美食筆記 CRUD — 編輯與刪除（預估 1 天）

### 要做的事
- 建立 `memo_edit.php`（編輯表單 + 處理邏輯）
- 建立 `memo_delete.php`（刪除邏輯）
- 實現圖片更新與舊圖清理

### 需要學習的技術與細節
1. **GET 參數傳遞 ID**
   - 編輯連結：`memo_edit.php?id=5`
   - PHP 讀取：`$id = $_GET['id']`
   - 用 PDO 預處理語句查詢該筆記錄，填入表單

2. **權限驗證（所有權檢查）**
   - 查出的筆記之 `user_id` 必須等於 `$_SESSION['user_id']`
   - 若不是自己的筆記，禁止編輯/刪除，顯示錯誤或跳轉

3. **UPDATE 操作**
   - 基本更新：`UPDATE dbmemo SET content=? WHERE id=? AND user_id=?`
   - 若有上傳新圖片：先刪除舊圖檔（原圖 + 縮圖），再儲存新圖
   - `unlink($filePath)`：刪除伺服器上的檔案
   - `file_exists()` 檢查檔案是否存在再刪除

4. **DELETE 操作**
   - 先查出該筆記的圖片路徑
   - 刪除實體檔案（原圖 + 縮圖）
   - 再執行 `DELETE FROM dbmemo WHERE id=? AND user_id=?`
   - 跳轉回首頁

5. **表單預填值**
   - 編輯頁面的 `<textarea>` 要預先填入原有內容
   - 若有圖片，顯示目前圖片的預覽
   - `value="<?= htmlspecialchars($memo['content']) ?>"`

---

## 第八階段：登入記錄查看頁面（預估 0.5 天）

### 要做的事
- 建立 `log_view.php`（以表格顯示登入歷史）

### 需要學習的技術與細節
1. **HTML 表格**
   - `<table>`, `<thead>`, `<tbody>`, `<tr>`, `<th>`, `<td>`
   - 用 PHP 迴圈產生每一行

2. **日期格式化**
   - MySQL 的 `DATETIME` 格式：`2026-04-18 14:30:00`
   - 可直接輸出，或用 PHP 的 `date()` + `strtotime()` 轉換格式

3. **狀態顯示**
   - `success` 欄位為 1 顯示 ✅，為 0 顯示 ❌
   - 三元運算子：`$log['success'] ? '✅' : '❌'`

---

## 第九階段：前端樣式設計（預估 1～2 天）

### 要做的事
- 建立 `assets/style.css`
- 設計深色主題的整體視覺風格
- 導覽列、表單、卡片、表格、提示訊息的 CSS

### 需要學習的技術與細節
1. **CSS 基礎**
   - 選擇器：元素、class（`.xxx`）、id（`#xxx`）、後代選擇器
   - 盒模型：`margin`, `padding`, `border`, `box-sizing: border-box`
   - `* { box-sizing: border-box; }` — 全域設定讓寬度計算更直覺

2. **CSS 變數（Custom Properties）**
   - 在 `:root` 中定義顏色變數：`--bg-color: #0f1117;`
   - 使用：`background: var(--bg-color);`
   - 好處：方便統一修改主題色

3. **Flexbox 排版**
   - 導覽列：`display: flex; justify-content: space-between; align-items: center;`
   - 表單元素水平排列：`display: flex; gap: 1rem;`

4. **CSS Grid 排版**
   - 卡片式佈局：`display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;`
   - 自動響應式：`auto-fill` + `minmax()` 讓卡片自動換行

5. **Google Fonts 引入**
   - `<link>` 標籤引入 "Noto Sans TC" 字體
   - `font-family: 'Noto Sans TC', sans-serif;`

6. **深色主題配色**
   - 背景：深色（`#0f1117`）
   - 文字：淺色（`#e0e0e0`）
   - 強調色：紫色（`#6c63ff`）
   - 輸入框、卡片背景：略深（`#1a1d27`）

7. **互動效果**
   - `transition: all 0.3s ease;` — 平滑過渡效果
   - `:hover` 狀態變化（按鈕顏色、卡片陰影）
   - `box-shadow` 做卡片浮起效果

8. **表單美化**
   - 自訂 `<input>`, `<textarea>`, `<select>` 的樣式
   - `<fieldset>` 和 `<legend>` 做群組分隔
   - focus 狀態：`outline: none; border-color: var(--accent);`

9. **提示訊息**
   - `.alert` 基礎樣式 + `.alert-error` / `.alert-success` 變化
   - 圓角、邊框、內距

---

## 第十階段：整合測試與收尾（預估 0.5～1 天）

### 要做的事
- 完整走過一遍所有流程：註冊 → 登入 → 新增筆記 → 編輯 → 刪除 → 查看日誌 → 登出
- 測試邊界情況與錯誤處理
- 確認安全性措施都有落實

### 需要檢查的項目
1. **功能面**
   - 註冊重複帳號是否被擋下
   - 密碼不一致、太短是否有提示
   - 未登入直接訪問 index.php 是否被重導向到登入頁
   - 嘗試編輯/刪除別人的筆記是否被擋下
   - 上傳非圖片檔案是否被拒絕
   - 刪除筆記後，圖片檔案是否從伺服器上移除

2. **安全面**
   - 所有 SQL 查詢是否都使用預處理語句（無直接拼接）
   - 所有輸出到頁面的資料是否都經過 `htmlspecialchars()`
   - 密碼是否使用 bcrypt 雜湊（資料庫中看不到明文）
   - 檔案上傳是否有做 MIME 類型檢查

---

## 建議的實作總時程

| 階段 | 內容 | 預估時間 |
|------|------|----------|
| 一 | 環境建置 + PHP 基礎 | 1～2 天 |
| 二 | 資料庫設計 | 1 天 |
| 三 | PHP 連線資料庫 | 0.5 天 |
| 四 | 用戶註冊 | 1～2 天 |
| 五 | 登入與 Session | 1～2 天 |
| 六 | 筆記列表 + 新增 + 圖片上傳 | 1～2 天 |
| 七 | 筆記編輯 + 刪除 | 1 天 |
| 八 | 登入記錄頁 | 0.5 天 |
| 九 | CSS 前端樣式 | 1～2 天 |
| 十 | 整合測試 | 0.5～1 天 |
| **合計** | | **約 8～13 天** |

---

## 推薦學習資源

- **PHP 官方文件**：https://www.php.net/manual/zh/ （中文版，最權威的參考）
- **W3Schools PHP**：https://www.w3schools.com/php/ （入門友善，有線上練習）
- **MDN Web Docs**：https://developer.mozilla.org/zh-TW/ （HTML/CSS 最佳參考）
- **SQL 教學**：https://www.w3schools.com/sql/ （SQL 語法快速入門）
- **CSS Flexbox**：https://css-tricks.com/snippets/css/a-guide-to-flexbox/
- **CSS Grid**：https://css-tricks.com/snippets/css/complete-guide-grid/

---

## 核心觀念小提醒

> **做這個專案最重要的三件事：**
> 1. **安全第一**：預處理語句防 SQL Injection、htmlspecialchars 防 XSS、bcrypt 雜湊密碼。這三個不是選配，是必須。
> 2. **理解 HTTP 請求流程**：瀏覽器發 GET/POST → Apache 接收 → PHP 處理 → 回傳 HTML。搞懂這條線，所有東西就串起來了。
> 3. **一次做一小塊**：不要想一次寫完整個系統。先讓資料庫連上、先讓一個表單能送出、先讓一張圖能上傳。每完成一小步就在瀏覽器測試，確認能動再往下走。
