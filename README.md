[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/VOLNfwbe)
# 作業 5 資料庫基礎存取

## 繳交說明
1. 分組名稱請依照分組表上進行更名，甲班為 A01, A02, .. A12 乙班則為 B01, B02, .. B11。更名若有問題，請找老師協助！
2. 分組作業，不開 PR, 所以也不開branch。
3. 繳交期限  4/20
4. 繳交後可以找時間整組找老師進行demo，請組員理解你們的程式碼，老師會個別問問題。
   
## 作業說明
自行設計題目，但須具備下列功能: 
1. 三個資料表，一個用來存放註冊資料，一個用來存放 log 資料，一個用來存放圖文備忘資料。
   資料表命名分別為 dbusers, dblog 及 dememo 。完成後請將資料表匯出，填入到資料夾中。
2. 具備註冊功能，註冊資料包含
   a. 帳號
   b. 暱稱
   c. 密碼
   d. 性別
   e. 興趣
   ...
3. 具備登入功能，需註冊後才能登入
4. 任何人登入時，紀錄登入者帳號，日期時間以及是否登入成功
5. 登入後可以新增圖文備忘，至少包含
   a. 新增者(使用者id)
   b. 多行文字
   c. 上傳一張圖片，進行縮圖後存放
   d. ...
6. 圖文備忘功能具備 新增、刪除、修改、列出
7. 登入資料可以被瀏覽

## 自行設計的內容說明(同學自填)
### 題目：個人私藏美食地圖系統

#### 系統簡介
本系統為「個人私藏美食地圖」，讓使用者可以記錄自己吃過的美食店家，搭配圖片與心得文字，打造屬於自己的美食筆記本。

#### 功能說明

| 功能 | 說明 |
|------|------|
| 註冊 | 填寫帳號、暱稱、密碼、性別、興趣後建立帳號，密碼以 bcrypt 加密儲存 |
| 登入 | 帳號密碼驗證，登入成功 / 失敗皆記錄至 `dblog`（含 IP、時間） |
| 登出 | 銷毀 session 後導回登入頁 |
| 新增美食記錄 | 輸入多行美食心得，可上傳 JPG/PNG 圖片，自動產生 200px 寬等比例縮圖 |
| 編輯美食記錄 | 修改內容與更換圖片，舊圖會自動刪除 |
| 刪除美食記錄 | 刪除資料庫記錄及對應的原圖 / 縮圖檔案，僅允許刪除自己的資料 |
| 瀏覽美食清單 | 以卡片方式列出所有美食記錄，顯示縮圖、內容、暱稱、時間 |
| 登入紀錄瀏覽 | 表格顯示所有登入紀錄（帳號、時間、成功/失敗、IP） |

#### 資料表結構

**dbusers** — 使用者註冊資料
| 欄位 | 型態 | 說明 |
|------|------|------|
| id | INT UNSIGNED, PK, AI | 使用者編號 |
| account | VARCHAR(50), UNIQUE | 帳號 |
| nickname | VARCHAR(50) | 暱稱 |
| password | VARCHAR(255) | 密碼（bcrypt 雜湊） |
| gender | ENUM('male','female','other') | 性別 |
| interests | VARCHAR(255) | 興趣（逗號分隔） |

**dblog** — 登入紀錄
| 欄位 | 型態 | 說明 |
|------|------|------|
| id | INT UNSIGNED, PK, AI | 紀錄編號 |
| account | VARCHAR(50) | 登入帳號 |
| login_at | DATETIME, DEFAULT NOW | 登入時間 |
| success | TINYINT(1) | 1=成功, 0=失敗 |
| ip | VARCHAR(45) | 登入 IP |

**dememo** — 圖文備忘（美食記錄）
| 欄位 | 型態 | 說明 |
|------|------|------|
| id | INT UNSIGNED, PK, AI | 備忘編號 |
| user_id | INT UNSIGNED, FK → dbusers.id | 新增者 |
| content | TEXT | 美食心得（多行文字） |
| image | VARCHAR(255), NULL | 圖片檔名 |
| created_at | DATETIME, DEFAULT NOW | 建立時間 |
| updated_at | DATETIME, ON UPDATE NOW | 更新時間 |

#### 檔案結構
```
db-a05/
├── config/
│   ├── auth.php          # Session 管理與登入驗證
│   └── db.php            # PDO 資料庫連線
├── SQL/
│   ├── dbusers.sql       # 使用者資料表 DDL
│   ├── dblog.sql         # 登入紀錄資料表 DDL
│   └── dbmemo.sql        # 圖文備忘資料表 DDL
├── assets/
│   └── style.css         # 全站深色主題樣式
├── uploads/
│   └── thumbs/           # 縮圖存放目錄
├── index.php             # 首頁（美食記錄清單）
├── login.php             # 登入頁面
├── login_process.php     # 登入驗證邏輯
├── register.php          # 註冊頁面
├── register_process.php  # 註冊處理邏輯
├── memo_add.php          # 新增美食記錄
├── memo_edit.php         # 編輯美食記錄
├── memo_delete.php       # 刪除美食記錄
├── log_view.php          # 登入紀錄瀏覽
└── logout.php            # 登出
```

#### 使用技術
- **後端**：PHP 8 + PDO (MySQL)
- **前端**：HTML5 + CSS3（深色主題、卡片式佈局）
- **安全**：密碼 bcrypt 加密、prepared statement 防 SQL Injection、htmlspecialchars 防 XSS
- **圖片處理**：GD Library（imagecopyresampled 等比例縮圖）