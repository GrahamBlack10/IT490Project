CREATE TABLE IF NOT EXISTS `Version` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `bundle_name` VARCHAR(255),
    `version` VARCHAR(50),
    `filename` VARCHAR(255),
    `status` VARCHAR(50),
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP
);
