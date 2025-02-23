CREATE TABLE IF NOT EXISTS `Forum_Comments`(
    `id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE,
    `forum_id` VARCHAR(10) , 
    `comment` VARCHAR(500) ,
    `user` VARCHAR(25) , 
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp
)