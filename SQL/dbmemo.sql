-- 資料表：dememo（圖文備忘 — 美食地圖記錄）
CREATE TABLE IF NOT EXISTS `dememo` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,              -- 外鍵，參考 dbusers.id
  `content`    TEXT         NOT NULL,               -- 備忘內容（美食心得）
  `image`      VARCHAR(255) DEFAULT NULL,           -- 圖片檔名
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_dememo_user` FOREIGN KEY (`user_id`)
    REFERENCES `dbusers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 