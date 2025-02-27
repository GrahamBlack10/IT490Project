CREATE TABLE IF NOT EXISTS `Movie_Reviews`(
    `id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE,
    `movie_id` VARCHAR(10) , 
    `rating` INT ,
    `review` VARCHAR(500) ,
    `user` VARCHAR(25) , 
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp
)