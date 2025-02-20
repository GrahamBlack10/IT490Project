CREATE TABLE IF NOT EXISTS `Movies`(
    `table_id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE,
    `imdb_id` VARCHAR(15) UNIQUE,
    `title` VARCHAR(100) UNIQUE,
    `releaseDate` VARCHAR(15),
    `image` VARCHAR(300),
    `url` VARCHAR(300),
    `description` VARCHAR(500),
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp
)