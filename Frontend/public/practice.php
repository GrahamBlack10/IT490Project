<?php
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

echo "Testing to see if I can get Username and User ID: " . PHP_EOL;

$session_key = session_id();
echo $session_key;









//$tmbd_id = '402431'; //Movie test

//$request = array();
//$request['type'] = 'get_movies';
//$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
//$response = $client->send_request($request);
//echo "Movie: $response" . PHP_EOL; //FOR GETTING MOVIES ONLY
//var_dump($response);


//$request = array();
//$request['type'] = 'get_movie_details';
//$request['movie_id'] = $tmbd_id;
//$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
//$response = $client->send_request($request);
//echo "Movie Details: $response"; //FOR GETTING MOVIE DETAILS ONLY
//var_dump($response);


?>


<form method="POST" action="send_sms.php">
  <label>Phone Number:</label>
  <input type="text" name="phone" placeholder="e.g. 5551234567" required><br>

  <label>Carrier:</label>
  <select name="carrier" required>
    <option value="vtext.com">Verizon</option>
    <option value="txt.att.net">AT&T</option>
    <option value="tmomail.net">T-Mobile</option>
    <option value="messaging.sprintpcs.com">Sprint</option>
  </select><br>

  <button type="submit">Send Code</button>
</form>
