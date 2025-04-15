CREATE TABLE IF NOT EXISTS `Version` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `bundle_name` VARCHAR(255) NOT NULL,
    `version_num` VARCHAR(50) NOT NULL,
    status ENUM('new', 'passed', 'failed') DEFAULT 'new'
);
