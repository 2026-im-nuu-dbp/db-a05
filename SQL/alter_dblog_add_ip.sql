-- 如果 dblog 已存在但缺少 ip 欄位，執行此腳本補上
ALTER TABLE `dblog` ADD COLUMN `ip` VARCHAR(45) NOT NULL DEFAULT '' AFTER `success`;
