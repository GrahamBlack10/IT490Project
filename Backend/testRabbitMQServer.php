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

function doRegistration($user, $password, $email)
{
  $mydb = new mysqli('127.0.0.1','testUser','12345','testdb');

  if ($mydb->errno != 0)
  {
          echo "failed to connect to database: ". $mydb->error . PHP_EOL;
          return "failure";
  }

  echo "successfully connected to database".PHP_EOL;

  $query = "INSERT INTO Users (username,password,email) VALUES ('$user','$password','$email')";

  if ($mydb->errno != 0)
  {
    echo "failed to execute query:".PHP_EOL;
    echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    return "failure";
  }

  if ($mydb->query($query) === TRUE)
  {
    echo "$user registered successfully." . PHP_EOL;
    echo "-------------------" . PHP_EOL;
    return "success";
  }

  else 
  {
    echo "Query failed";
    return "failure";
  }
}

function createSession($id, $user, $session_id) {
  $mydb = new mysqli('127.0.0.1','testUser','12345','testdb');
  $query = "INSERT INTO Sessions (session_id,user_id,username) VALUES ('$session_id','$id','$user')";
    
    if ($mydb->errno != 0) {
      echo "failed to execute query:".PHP_EOL;
      echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
      return "failure";
    }

    if ($mydb->query($query) === TRUE) {
      return "success";
    }

    else {
      echo "session could not be created" . PHP_EOL;
      echo "-------------------" . PHP_EOL;
      return "failure";
    }
}

function verifySession($session_id) {
  $mydb = new mysqli('127.0.0.1','testUser','12345','testdb');

  if ($mydb->errno != 0)
  {
    echo "failed to connect to database: ". $mydb->error . PHP_EOL;
    return "failure";
  }

  echo "successfully connected to database".PHP_EOL;

  $query = "SELECT session_id FROM Sessions WHERE session_id='$session_id'";
  $result = $mydb->query($query);

  if ($result->num_rows == 0) {
    echo "session not found" . PHP_EOL;
    echo "-------------------" . PHP_EOL;
    return "failure";
  }
  else {
    echo "session found" . PHP_EOL;
    echo "-------------------" . PHP_EOL;
    return "success";
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

function getMovies($filter) {
  // Will need this once we have data in the Movies table
}

function getMovieDetails($movie_id) {
  // Will need this once we have data in the Movies table
}

function populateDatabase($data) {
  try {
      $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $query = "INSERT INTO Movies (imdb_id, title, description, image, releaseDate, genre) 
                VALUES (:imdb_id, :title, :description, :image, :release_date, :genre)";
      
      // Loop through each movie in the data
      foreach ($data ['results']as $movie) {
          // Check for duplicates (based on imdb_id)
          $checkQuery = "SELECT COUNT(*) FROM Movies WHERE imdb_id = :imdb_id";
          $stmt = $pdo->prepare($checkQuery);
          $stmt->execute([':imdb_id' => $movie['imdb_id']]);
          $count = $stmt->fetchColumn();

          if ($count == 0) {
              // If no duplicate, insert the movie
              $stmt = $pdo->prepare($query);
              $stmt->execute([
                  ':imdb_id' => $movie['imdb_id'],
                  ':title' => $movie['title'],
                  ':description' => $movie['overview'],
                  ':image' => $movie['poster_path'],
                  ':release_date' => $movie['release_date'],
                  ':vote average' => $movie['vote_average'] 
              ]);

              echo "Inserted movie: " . $movie['title'] . PHP_EOL;
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
      var_dump($result);
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
      return getMovies($request["filter"]);
    case "get_movie_details":
      return getMovieDetails($request['movie_id']);
    case "get_user_id":
      return getUserID($request['session_id']);
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
