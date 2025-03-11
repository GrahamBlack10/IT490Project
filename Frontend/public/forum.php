<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}
?>

<div class="container py-5">
    <!-- Forum Creation Form -->
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    Create New Forum Post
                </div>
                <div class="card-body">
                    <form action="forum.php" method="POST">
                        <div class="mb-3">
                            <label for="forum_title" class="form-label">Title</label>
                            <input type="text" class="form-control" name="forum_title" id="forum_title">
                        </div>
                        <div class="mb-3">
                            <label for="forum_description" class="form-label">Description</label>
                            <textarea class="form-control" name="forum_description" id="forum_description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Forum Post</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
            echo '<div class="alert alert-success text-center" role="alert">Post created!</div>';
        }
    }
    
   
    $request = array();
    $request['type'] = 'get_forums';
    $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
    $forums = $client->send_request($request);
    ?>

    
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h3 class="mb-4 text-center">Forum Posts</h3>
            <?php if (!empty($forums)): ?>
                <?php foreach ($forums as $forum): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="forum_post.php?id=<?php echo $forum['id']; ?>">
                                    <?php echo $forum['title']; ?>
                                </a>
                            </h5>
                            <h6 class="card-subtitle mb-2 text-muted">Author: <?php echo $forum['user']; ?></h6>
                            <p class="card-text"><?php echo $forum['description']; ?></p>
                            <p class="card-text"><small class="text-muted"><?php echo $forum['created']; ?></small></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No forum posts found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
