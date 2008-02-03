--
-- Creating the DobaLog table
--

CREATE TABLE `DobaLog` (
	`doba_log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, 
	`datatype` ENUM('order','product') NOT NULL DEFAULT 'order', 
	`local_id` INT UNSIGNED NOT NULL DEFAULT '0', 
	`xfer_method` ENUM('file','api') NOT NULL DEFAULT 'file', 
	`ymdt` DATETIME NOT NULL, 
	`filename` VARCHAR(30) NOT NULL, 
	`api_response` TEXT NOT NULL, 
	PRIMARY KEY (`doba_log_id`), 
	INDEX (`datatype`, `local_id`, `xfer_method`, `filename`)
) ENGINE = MyISAM;