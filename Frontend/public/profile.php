<?php
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";

if (!is_logged_in()) {
    die(header("Location: login.php"));
}

$request = array();
$request['type'] = 'get_favorite_genre';
$request['session_id'] = session_id();
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);
if ($response === 'No genre found') {
    $output = 'No genre selected!';
}

else {
    $output = $response['genre'];
}

?>

<br>
<label for="GenreOutput">Your favorite genre is: <?php echo $output ?></label>

<br>
<form action="profile.php" method="POST">
    <label for="GenreInput">Update your favorite genre: </label>
    <select class="form-control" name="genre" id="genre">
      <option>Adventure</option>
      <option>Fantasy</option>
      <option>Drama</option>
      <option>Horror</option>
      <option>Comedy</option>
    </select>
    <br>
    <button type="submit" class="btn btn-primary">Update Genre</button>
</form>

<?php
if (isset($_POST["genre"])) {
    $request = array();
    $request["type"] = "update_favorite_genre";
    $request["genre"] = $_POST["genre"];
    $request["session_id"] = session_id();
    $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);
    header('Location: profile.php');
    exit;
}

?>