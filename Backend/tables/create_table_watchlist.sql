CREATE TABLE IF NOT EXISTS `Watchlist`(
    `movie_id` VARCHAR(10) , 
    `image` VARCHAR(200) ,
    `user` VARCHAR(25) , 
    FOREIGN KEY(movie_id) REFERENCES Movies(tmdb_id)
);