#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username,$password,$session_id)
{
  $mydb = new mysqli('127.0.0.1','testUser','12345','testdb');

  if ($mydb->errno != 0)
  {
    echo "failed to connect to database: ". $mydb->error . PHP_EOL;
    return "failure";
  }

  echo "successfully connected to database".PHP_EOL;

  $query = "SELECT id,username,password FROM Users WHERE username='$username'";
  $result = $mydb->query($query);
  if ($result->num_rows == 0) {
    echo "Username not found";
    echo "-------------------" . PHP_EOL;
    return "failure";
  }
  $user = mysqli_fetch_array($result);
  if (password_verify($password, $user["password"])) {
    echo 'user and password found in database' . PHP_EOL;
    createSession($user['id'],$user['username'], $session_id);
    echo 'session created,' . $username . ' has logged in' . PHP_EOL;
    echo "-------------------" . PHP_EOL;
    return "success";
  }

  else {
    echo 'password is incorrect' . PHP_EOL;
    echo "-------------------" . PHP_EOL;
    return "failure";
  }
}


function doRegistration($user, $password, $email) {
  try {
      $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $query = "INSERT INTO Users (username, password, email) VALUES (:username, :password, :email)";
      $stmt = $pdo->prepare($query);
      $stmt->execute([
          ':username' => $user,
          ':password' => $password, 
          ':email' => $email
      ]);

      echo "$user registered successfully." . PHP_EOL;
      return "success";
  } catch (PDOException $e) {
      echo "registration failed: " . $e->getMessage() . PHP_EOL;
      return "failure";
  }
  $pdo = null;
}

function createSession($id, $user, $session_id) {
  try {
      $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $query = "INSERT INTO Sessions (session_id, user_id, username) VALUES (:session_id, :user_id, :username)";
      $stmt = $pdo->prepare($query);
      $stmt->execute([
          ':session_id' => $session_id,
          ':user_id' => $id,
          ':username' => $user
      ]);

      $pdo = null;
      return "success";
  } catch (PDOException $e) {
      echo "Failed to create session: " . $e->getMessage() . PHP_EOL;
      return "failure";
  }
}


function verifySession($session_id) {
  try {
      $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $query = "SELECT session_id FROM Sessions WHERE session_id = :sessionID";
      $stmt = $pdo->prepare($query);
      $stmt->execute([
          ':sessionID' => $session_id
      ]);

      if ($stmt->rowCount() == 0) {
          echo "Session not found" . PHP_EOL;
          echo "-------------------" . PHP_EOL;
          return "failure";
      } else {
          echo "Session found" . PHP_EOL;
          echo "-------------------" . PHP_EOL;
          return "success";
      }
  } catch (PDOException $e) {
      echo "Database error: " . $e->getMessage() . PHP_EOL;
      return "failure";
  }

}


function getUserID($session_id) {
  try{
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $n = "SELECT user_id FROM Sessions where session_id = :sessionID";
    $stmt = $pdo->prepare($n);
    $stmt->execute ([
              ':sessionID' => $session_id
            ]);
    if ($stmt->rowCount() > 0) { //Checks if rows are returned first and then fetches the AA
      $session = $stmt->fetch(PDO::FETCH_ASSOC);
      return $session['user_id'];
    } else {
      return "No UserID for this session";
    }
  } catch (PDOException $e) {
    echo "Fetch userID error: " . $e->getMessage() . PHP_EOL;
  }
  $pdo = null;
  return null;
}

function getUsername($session_id) {
  try{
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $n = "SELECT username FROM Sessions where session_id = :sessionID";
    $stmt = $pdo->prepare($n);
    $stmt->execute ([
      ':sessionID' => $session_id
    ]);
    if ($stmt->rowCount() > 0) { //Checks if rows are returned first and then fetches the AA
      $session = $stmt->fetch(PDO::FETCH_ASSOC);
      return $session['username'];
    } else{
      return "No UserName for this session";
    }
  } catch (PDOException $e) {
      echo "Fetch userID error: " . $e->getMessage() . PHP_EOL;
  }
  $pdo = null;
  return null;
}

function getMovies() {
  // Will need this once we have data in the Movies table
  try{
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $movie = "SELECT * FROM Movies";
    $stmt = $pdo-> prepare($movie);
    $stmt->execute ([
      
    ]);
    if ($stmt->rowCount()>0){
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    } else {
      return "No movies can be displayed at this time.";
    }

  } catch (PDOException $e) {
    echo "Fetch userID error:" . $e->getMessage() . PHP_EOL;
  }
  $pdo = null;
  return null;
}

function getMoviesWithFilter($filter) {
  try{
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $movie = "SELECT * FROM Movies WHERE title = :title";
    $stmt = $pdo-> prepare($movie);
    $stmt->execute ([
      ':title' => '%$filter%'
    ]);
    if ($stmt->rowCount()>0){
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    } else {
      return "No movies can be displayed at this time.";
    }

  } catch (PDOException $e) {
    echo "Fetch movies error:" . $e->getMessage() . PHP_EOL;
  }
  $pdo = null;
  return null;
}

function getMovieDetails($tmdb_id) {
  // Will need this once we have data in the Movies table
  try{
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $movie = "SELECT * FROM Movies where tmdb_id = :tmdbID";
    $stmt = $pdo->prepare($movie);
    $stmt->execute ([
      ':tmdbID' => $tmdb_id
    ]);
    if ($stmt->rowCount() > 0) { //Checks if rows are returned first and then fetches the AA
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result;
    } else{
      return "No movie Details available";
    }
  } catch (PDOException $e) {
      echo "Fetch userID error: " . $e->getMessage() . PHP_EOL;
  }
  $pdo = null;
  return null;
}

function createMovieReview($session_id, $movie_id, $rating, $review) {
  try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = "INSERT INTO Movie_Reviews (movie_id, rating, review, user) 
              VALUES (:movie_id, :rating, :review, :user)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':movie_id' => $movie_id,
        ':rating' => $rating,
        ':review' => $review,
        ':user' => getUsername($session_id)
    ]);
    
    echo "Movie review created" . PHP_EOL;
  } catch (PDOException $e) {
      echo "Database error: " . $e->getMessage() . PHP_EOL;
  }

  $pdo = null;
  return "Review created";
}

function getMovieReviews($movie_id) {
  try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $query = "SELECT * FROM Movie_Reviews WHERE movie_id = :movie_id";
    $stmt = $pdo->prepare($query);
    $r = $stmt->execute([
      ':movie_id' => $movie_id,
    ]);

    if ($r) {
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      echo "Returned all movie reviews" . PHP_EOL;
      return $result;
    }
    
    else {
      echo "what the fuck" . PHP_EOL;
    }
  } catch (PDOException $e) {
      echo "Database error: " . $e->getMessage() . PHP_EOL;
      return 'error';
  }
}

function getAverageRating($movie_id) {
  try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $query = "SELECT AVG(rating) as average_rating FROM Movie_Reviews WHERE movie_id = :movie_id";
    $stmt = $pdo->prepare($query);
    $r = $stmt->execute([
      ':movie_id' => $movie_id,
    ]);

    if ($r) {
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      var_dump($result);
      echo "Returned average rating for $movie_id" . PHP_EOL;
      return $result;
    }
    
    else {
      echo "what the fuck" . PHP_EOL;
    }
  } catch (PDOException $e) {
      echo "Database error: " . $e->getMessage() . PHP_EOL;
      return 'error';
  }
}

function updateFavoriteGenre($genre, $session_id) {
  try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $user = getUsername($session_id);
    $query = "SELECT * FROM Favorite_Genres where username = :username";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':username' => $user,
    ]);
    
    if ($stmt->rowCount() > 0) {
      echo 'Updating favorite genre...'. PHP_EOL;
      $query = "UPDATE Favorite_Genres SET genre = :genre WHERE username = :username";
      $stmt = $pdo->prepare($query);
      $stmt->execute([
        ':genre' => $genre,
        ':username' => $user
      ]);
      echo 'Favorite genre has been updated!' . PHP_EOL;
      return 'Favorite genre updated';
    }

    else {
      echo 'Creating favorite genre...'. PHP_EOL;
      $query = "INSERT INTO Favorite_Genres (username, genre) 
                VALUES (:username, :genre)";

      $stmt = $pdo->prepare($query);
      $stmt->execute([
          ':username' => $user,
          ':genre' => $genre   
      ]);
      echo 'Favorite genre has been created!'. PHP_EOL;
      return 'Favorite genre created';
    }

  } catch (PDOException $e) {
      echo "Database error: " . $e->getMessage() . PHP_EOL;
      return 'WHAT THE FUCK';
  }
}

function getFavoriteGenre($session_id) {
  try{
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $user = getUsername($session_id);
    $movie = "SELECT genre FROM Favorite_Genres where username = :username";
    $stmt = $pdo->prepare($movie);
    $stmt->execute ([
      ':username' => $user
    ]);
    if ($stmt->rowCount() == 0) { 
      return 'No genre found';
    } else{
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result;
    }
  } catch (PDOException $e) {
      echo "Error getting favorite genre" . $e->getMessage() . PHP_EOL;
  }

  $pdo = null;
  return null;
}

function getRecommendations($session_id) {
  try{
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $genre = getFavoriteGenre($session_id)['genre'];
    $param = '%'.$genre.'%';
    echo 'Finding movies for ' . $genre . PHP_EOL;
    $query = "SELECT tmdb_id,image FROM Movies WHERE genre_ids LIKE :genre LIMIT 3";
    $stmt = $pdo->prepare($query);
    $stmt->execute ([
      ':genre' => $param
    ]);
    if ($stmt->rowCount()>0){
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    } else {
      echo 'Could not find any movies for ' . $genre . PHP_EOL;
      return "No movies can be displayed at this time.";
    }
    
  } catch (PDOException $e) {
      echo "Error getting favorite genre" . $e->getMessage() . PHP_EOL;
  }

  $pdo = null;
  return null;
}

function populateDatabase($data) {
  try {
      $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $query = "INSERT INTO Movies (tmdb_id, title, description, image, releaseDate, vote_average, genre_ids) 
                VALUES (:tmdb_id, :title, :description, :image, :release_date, :vote_average, :genre_ids)";
      
      // Loop through each movie in the data
      foreach ($data['results'] as $movie) {
          // Check for duplicates (based on tmdb_id)
          $checkQuery = "SELECT COUNT(*) FROM Movies WHERE tmdb_id = :tmdb_id";
          $stmt = $pdo->prepare($checkQuery);
          $stmt->execute([':tmdb_id' => $movie['id']]);
          $count = $stmt->fetchColumn();

          if ($count == 0) {
              // Map genre ids to genre names
              $genreNames = [];
              foreach ($movie['genre_ids'] as $genreId) {
                  $genreName = getGenreNameById($genreId);
                  if ($genreName) {
                      $genreNames[] = $genreName;
                  } else {
                      echo "Genre ID $genreId not found. Skipping.\n";
                  }
              }

              // Join the genre names into a string
              $genreNamesString = implode(', ', $genreNames);

              // If no duplicate, insert the movie
              $stmt = $pdo->prepare($query);
              $stmt->execute([
                  ':tmdb_id' => $movie['id'],
                  ':title' => $movie['title'],
                  ':description' => $movie['overview'],
                  ':image' => $movie['poster_path'],
                  ':release_date' => $movie['release_date'],
                  ':vote_average' => $movie['vote_average'],
                  ':genre_ids' => $genreNamesString // Store genre names as a string
              ]);

              echo "Inserted movie: " . $movie['title'] . " with genres: " . $genreNamesString . PHP_EOL;
          } else {
              echo "Duplicate movie skipped: " . $movie['title'] . PHP_EOL;
          }
      }

  } catch (PDOException $e) {
      echo "Database error: " . $e->getMessage() . PHP_EOL;
  }

  $pdo = null;
  return "success";
}

function getGenreNameById($genreId) {
  $genreNames = [
      28 => "Action",
      12 => "Adventure",
      16 => "Animation",
      35 => "Comedy",
      80 => "Crime",
      99 => "Documentary",
      18 => "Drama",
      10751 => "Family",
      14 => "Fantasy",
      36 => "History",
      27 => "Horror",
      10402 => "Music",
      9648 => "Mystery",
      53 => "Thriller",    
      878 => "Science Fiction",  
      10752 => "War",    
      10749 => "Romance",  
     
  ];

  return $genreNames[$genreId] ?? null;  // Return the genre name or null if not found
}



function createForum($title, $description, $session_id) {
  try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = "INSERT INTO Forums (title, description, user) 
              VALUES (:title, :description, :user)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':user' => getUsername($session_id),
    ]);
    
    echo "Forum post created" . PHP_EOL;
  } catch (PDOException $e) {
      echo "Database error: " . $e->getMessage() . PHP_EOL;
  }

  $pdo = null;
  return "success";
}

function getForums() {
  try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $query = "SELECT * FROM Forums";
    $stmt = $pdo->prepare($query);
    $r = $stmt->execute([]);

    if ($r) {
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      echo "Returned all forum titles and descriptions" . PHP_EOL;
      return $result;
    }
    
    else {
      echo "what the fuck" . PHP_EOL;
    }
  } catch (PDOException $e) {
      echo "Database error: " . $e->getMessage() . PHP_EOL;
      return 'error';
  }
}

function getForumPost($id) {
  try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $query = "SELECT * FROM Forums WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $r = $stmt->execute([
      'id' => $id,
    ]);

    if ($r) {
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      var_dump($result);
      echo "Returned forum number " . $id . PHP_EOL;
      return $result;
    }
    
    else {
      echo "what the fuck" . PHP_EOL;
    }
  } catch (PDOException $e) {
      echo "Database error: " . $e->getMessage() . PHP_EOL;
      return 'error';
  }
}

function createForumComment($comment, $forum_id, $session_id) {
  try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = "INSERT INTO Forum_Comments (forum_id, comment, user) 
              VALUES (:forum_id, :comment, :user)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':forum_id' => $forum_id,
        ':comment' => $comment,
        ':user' => getUsername($session_id),
    ]);
    
    echo "Forum comment created" . PHP_EOL;
  } catch (PDOException $e) {
      echo "Database error: " . $e->getMessage() . PHP_EOL;
  }

  $pdo = null;
  return "success";
}

function getForumComments($forum_id) {
  try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $query = "SELECT * FROM Forum_Comments WHERE forum_id = :forum_id";
    $stmt = $pdo->prepare($query);
    $r = $stmt->execute([
      'forum_id' => $forum_id,
    ]);

    if ($r) {
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      var_dump($result);
      echo "Returned all forum comments" . PHP_EOL;
      return $result;
    }
    
    else {
      echo "what the fuck" . PHP_EOL;
    }
  } catch (PDOException $e) {
      echo "Database error: " . $e->getMessage() . PHP_EOL;
      return 'error';
  }
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['user'],$request['password'],$request['session_id']);
    case "registration":
      return doRegistration($request['user'],$request['password'],$request['email']);
    case "validate_session":
      return verifySession($request['session_id']);
    case "populate_database":
      return populateDatabase($request['data']);
    case "get_movies":
      return getMovies();
    case "get_movies_with_filter":
      return getMoviesWithFilter($request['filter']);
    case "get_movie_details":
      return getMovieDetails($request['movie_id']);
    case "create_movie_review":
      return createMovieReview($request['session_id'], $request['movie_id'], $request['rating'], $request['review']);
    case "get_movie_reviews":
      return getMovieReviews($request['movie_id']);
    case "get_average_rating":
      return getAverageRating($request['movie_id']);
    case "update_favorite_genre":
      return updateFavoriteGenre($request['genre'], $request['session_id']);
    case "get_favorite_genre":
      return getFavoriteGenre($request['session_id']);  
    case "get_recommendations":
      return getRecommendations($request['session_id']);  
    case "get_username":
      return getUsername($request['session_id']);
    case "create_forum":
      return createForum($request['title'], $request['description'], $request['session_id']);
    case "get_forums":
      return getForums();
    case "get_forum_post":
      return getForumPost($request['id']);
    case "create_forum_comment":
      return createForumComment($request['comment'], $request['forum_id'], $request['session_id']);
    case "get_forum_comments":
      return getForumComments($request['forum_id']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>
