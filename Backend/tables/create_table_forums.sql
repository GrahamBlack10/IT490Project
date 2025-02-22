CREATE TABLE IF NOT EXISTS `Forums`(
    `id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE,
    `title` VARCHAR(100) UNIQUE,
    `description` VARCHAR(500) UNIQUE,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp
)