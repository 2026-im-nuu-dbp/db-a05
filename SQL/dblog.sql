-- 資料表：dblog（登入 log）
CREATE TABLE IF NOT EXISTS dblog (
  `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `account`  VARCHAR(50)  NOT NULL     , -- 登入帳號
  `login_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ,-- 登入時間
  `success`  TINYINT(1)   NOT NULL DEFAULT 0 , -- 1=成功 0=失敗
  `ip`       VARCHAR(45)  NOT NULL DEFAULT '' -- 登入 IP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;