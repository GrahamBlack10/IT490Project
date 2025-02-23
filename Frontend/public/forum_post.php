<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

if (!is_logged_in()) {
    die(header("Location: login.php"));
}

$id = (int)$_GET['id'];
$request = array();
$request["type"] = "get_forum_post";
$request["id"] = $id;
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);

$title = $response['title'];
$user = $response['user'];
$created = $response['created'];
$description = $response['description'];
?>

<ul class="list-group">
  <li class="list-group-item"><?php echo $title; ?></li>
  <li class="list-group-item"><?php echo $user; ?></li>
  <li class="list-group-item"><?php echo $created; ?></li>
  <li class="list-group-item"><?php echo $description; ?></li>
</ul>

<form action="forum_post.php?id=<?php echo $id; ?>" method="POST">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">Comment</span>
        </div>
    <textarea class="form-control" name="comment" id="comment"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Add comment</button>
</form>

<?php
if (isset($_POST['comment'])) {
    $request = array();
    $request['type'] = 'create_forum_comment';
    $request['comment'] = $_POST['comment'];
    $request['forum_id'] = $id;
    $request['session_id'] = session_id();

    $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);
}

$request['type'] = 'get_forum_comments';
$request['forum_id'] = $id; 
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);
var_dump($response);


?>