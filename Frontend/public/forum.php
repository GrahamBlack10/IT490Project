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

?>

<form action="forum.php" method="POST">
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroup-sizing-default">Title</span>
        </div>
    <input type="text" class="form-control" name="forum_title" id="forum_title" aria-describedby="inputGroup-sizing-default">
    </div>

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">Description</span>
        </div>
    <textarea class="form-control" name="forum_description" id="forum_description"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Add forum</button>
</form>

<?php
    if (isset($_POST["forum_title"]) && isset($_POST["forum_description"])) {
        $title = $_POST["forum_title"];
        $description = $_POST["forum_description"];
        $request = array();
        $request['type'] = 'create_forum'; 
        $request['title'] = $title;
        $request['description'] = $description;
        $request['session_id'] = session_id();
        $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
        $response = $client->send_request($request);

        if ($response === 'success') {
            echo 'Post created!';
        }
    }

    $request = array();
    $request['type'] = 'get_forums';
    $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);

    foreach ($response as $forum) { 
    ?>
        <ul class="list-group">
        <li class="list-group-item"><a href="forum_post.php?id=<?php echo $forum['id']?>"><?php echo $forum['title']?></a></li>
        <li class="list-group-item"><?php echo "Author: " . $forum['user']?></li>
        <li class="list-group-item"><?php echo $forum['created']?></li>
        <li class="list-group-item"><?php echo $forum['description']?><br/><br/></li>
        </ul>
<?php 
    }
?>