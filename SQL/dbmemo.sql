-- 資料表：dememo（圖文備忘）
CREATE TABLE IF NOT EXISTS `dememo` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL               COMMENT '新增者（dbusers.id）',
  `content`    TEXT         NOT NULL               COMMENT '多行文字備忘',
  `image`      VARCHAR(255) DEFAULT NULL           COMMENT '縮圖檔名',
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_dememo_user` FOREIGN KEY (`user_id`)
    REFERENCES `dbusers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;