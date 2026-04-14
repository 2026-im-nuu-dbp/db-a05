-- 資料表：dbusers（註冊資料）
CREATE TABLE IF NOT EXISTS `dbusers` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `account`    VARCHAR(50)     NOT NULL UNIQUE COMMENT '帳號',
  `nickname`   VARCHAR(50)     NOT NULL        COMMENT '暱稱',
  `password`   VARCHAR(255)    NOT NULL        COMMENT 'bcrypt 雜湊密碼',
  `gender`     ENUM('male','female','other') NOT NULL DEFAULT 'other' COMMENT '性別',
  `interests`  VARCHAR(255)    DEFAULT NULL    COMMENT '興趣，逗號分隔',
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
