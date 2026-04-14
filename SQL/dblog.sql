-- 資料表：dblog（登入 log）
CREATE TABLE IF NOT EXISTS `dblog` (
  `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `account`  VARCHAR(50)  NOT NULL        COMMENT '登入帳號',
  `login_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登入時間',
  `success`  TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '1=成功 0=失敗',
  `ip`       VARCHAR(45)  DEFAULT NULL    COMMENT '來源 IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;