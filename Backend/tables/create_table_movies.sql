CREATE TABLE IF NOT EXISTS `Movies`(
    `table_id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE,
    `tmdb_id` VARCHAR(15) UNIQUE,
    `title` VARCHAR(100) UNIQUE,
    `releaseDate` VARCHAR(15),
    `image` TEXT,
    `description` TEXT,
    `vote_average` FLOAT,
    `genre_ids` TEXT,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp
);