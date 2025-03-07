CREATE TABLE IF NOT EXISTS `Forums`(
    `id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE,
    `title` VARCHAR(100) ,
    `description` VARCHAR(500) ,
    `user` VARCHAR(25) , 
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp
)