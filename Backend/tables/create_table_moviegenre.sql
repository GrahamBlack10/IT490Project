CREATE TABLE IF NOT EXISTS `MovieGenres` (
    `movie_id` INT,
    `genre_id` INT,
    PRIMARY KEY (`movie_id`, `genre_id`),
    FOREIGN KEY (`movie_id`) REFERENCES `Movies`(`table_id`) ON DELETE CASCADE,
    FOREIGN KEY (`genre_id`) REFERENCES `Genres`(`id`) ON DELETE CASCADE
);
