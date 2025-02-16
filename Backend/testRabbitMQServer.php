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
    return "failure";
  }
  $user = mysqli_fetch_array($result);
  if (password_verify($password, $user["password"])) {
    echo 'user and password found in database' . PHP_EOL;
    createSession($user['id'],$user['username'], $session_id);
    echo 'session crated, user has logged in';
    return "success";
  }

  else {
    echo 'password is incorrect' . PHP_EOL;
    return "failure";
  }
}

function doRegistration($user, $password)
{
  $mydb = new mysqli('127.0.0.1','testUser','12345','testdb');

  if ($mydb->errno != 0)
  {
          echo "failed to connect to database: ". $mydb->error . PHP_EOL;
          return "failure";
  }

  echo "successfully connected to database".PHP_EOL;

  $query = "INSERT INTO Users (username,password) VALUES ('$user','$password')";

  if ($mydb->errno != 0)
  {
          echo "failed to execute query:".PHP_EOL;
          echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
          return "failure";
  }

  if ($mydb->query($query) === TRUE)
  {
    echo "$user registered successfully." . PHP_EOL;
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
      echo "session created" . PHP_EOL;
      echo $user . ' has logged in' . PHP_EOL;
      return "success";
    }

    else {
      echo "session could not be created" . PHP_EOL;
      return "failure";
    }
}

function doSessionVerification($session_id) {
  

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
      return doRegistration($request['user'],$request['password']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>
