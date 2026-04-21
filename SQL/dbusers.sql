-- 資料表：dbusers（註冊資料）
CREATE TABLE IF NOT EXISTS `dbusers` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `account`    VARCHAR(50)     NOT NULL UNIQUE,
  `nickname`   VARCHAR(50)     NOT NULL        ,
  `password`   VARCHAR(255)    NOT NULL        ,
  `gender`     ENUM('male','female','other') NOT NULL   ,
  `interests`  VARCHAR(255)    NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
